<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cidade;
use App\Models\Qualidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MercadoNegroController extends Controller
{
    private const BLACK_MARKET_CITY_ID = 8;

    private const VALID_SORTS = [
        'item_nome'        => 'item_ingles',
        'qualidade_nome'   => 'qualidade_id',
        'menor_ordem'      => 'menor_ordem',
        'menor_valor'      => 'menor_valor',
        'maior_valor'      => 'maior_valor',
        'lucro_ordem'      => 'lucro_ordem',
        'pct_lucro_ordem'  => 'pct_lucro_ordem',
        'lucro_direto'     => 'lucro_direto',
        'pct_lucro_direto' => 'pct_lucro_direto',
        'total_vendidos'   => 'total_vendidos',
    ];

    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100, 250])
            ? (int) $request->input('per_page')
            : 50;
        $page   = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $perPage;

        $sortKey = $request->input('sort', 'lucro_direto');
        if (! array_key_exists($sortKey, self::VALID_SORTS)) {
            $sortKey = 'lucro_direto';
        }
        $sortDir    = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortDirSql = strtoupper($sortDir);
        $sortCol    = self::VALID_SORTS[$sortKey];

        $busca          = $request->input('busca');
        $categoriaId    = $request->input('categoria');
        $qualidadeId    = $request->input('qualidade');
        $cidadeOrdemId  = $request->input('cidade_ordem');
        $cidadeCompraId = $request->input('cidade_compra');

        // Mercado Negro always sells to Black Market; these are fixed defaults
        $pctMinLucroDireto = $request->input('pct_min_lucro_direto', '50');
        $qtdMinVendidos    = $request->input('qtd_min_vendidos', '1');

        [$whereParts, $outerBindings] = $this->buildFilters(
            $busca, $categoriaId, $qualidadeId,
            $pctMinLucroDireto, $qtdMinVendidos
        );

        $whereClause = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

        [$cte, $cteBindings] = $this->cteDefinition(
            $cidadeOrdemId  ? (int) $cidadeOrdemId  : null,
            $cidadeCompraId ? (int) $cidadeCompraId : null,
        );

        $bindings = array_merge($cteBindings, $outerBindings);

        $total   = (int) DB::selectOne("{$cte} SELECT COUNT(*) AS cnt FROM base {$whereClause}", $bindings)->cnt;
        $results = DB::select(
            "{$cte} SELECT * FROM base {$whereClause} ORDER BY {$sortCol} {$sortDirSql} LIMIT ? OFFSET ?",
            array_merge($bindings, [$perPage, $offset])
        );

        $totalPages = max(1, (int) ceil($total / $perPage));

        $categoriasTree = Categoria::whereNull('categoria_pai_id')
            ->with('filhos.filhos')
            ->orderBy('ordem')
            ->orderBy('portugues')
            ->get();
        $qualidades = Qualidade::orderBy('id')->get();
        $cidades    = Cidade::where('id', '!=', self::BLACK_MARKET_CITY_ID)->orderBy('portugues')->get();

        return view('itens.mercado-negro', compact(
            'results', 'total', 'page', 'perPage', 'totalPages',
            'categoriasTree', 'qualidades', 'cidades',
            'sortKey', 'sortDir',
            'busca', 'categoriaId', 'qualidadeId',
            'cidadeOrdemId', 'cidadeCompraId',
            'pctMinLucroDireto', 'qtdMinVendidos'
        ));
    }

    private function cteDefinition(?int $cidadeOrdemId, ?int $cidadeCompraId): array
    {
        $cteBindings = [];
        $ordemExtra  = '';
        $compraExtra = '';

        if ($cidadeOrdemId) {
            $ordemExtra    = 'AND cidade_id = ?';
            $cteBindings[] = $cidadeOrdemId;
        }
        if ($cidadeCompraId) {
            $compraExtra   = 'AND cidade_id = ?';
            $cteBindings[] = $cidadeCompraId;
        }

        // Black Market city id bound for the venda CTE
        $cteBindings[] = self::BLACK_MARKET_CITY_ID;

        $sql = "
            WITH
            ranked_ordem AS (
                SELECT item_id, qualidade_id, cidade_id, ordem_de_compra,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY ordem_de_compra ASC) AS rn
                FROM items_precos
                WHERE ordem_de_compra > 0
                  AND cidade_id NOT IN (SELECT id FROM cidades WHERE ingles = 'Black Market')
                  {$ordemExtra}
            ),
            ranked_min_valor AS (
                SELECT item_id, qualidade_id, cidade_id, valor,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY valor ASC) AS rn
                FROM items_precos
                WHERE valor > 0
                  AND cidade_id NOT IN (SELECT id FROM cidades WHERE ingles = 'Black Market')
                  {$compraExtra}
            ),
            ranked_bm_valor AS (
                SELECT item_id, qualidade_id, cidade_id, preco_medio AS valor,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY preco_medio DESC) AS rn
                FROM items_precos
                WHERE preco_medio > 0
                  AND cidade_id = ?
            ),
            item_stats AS (
                SELECT item_id, qualidade_id,
                       SUM(quantidade_itens_vendidos) AS total_vendidos,
                       ROUND(AVG(NULLIF(preco_medio, 0)), 0) AS preco_medio
                FROM items_precos
                GROUP BY item_id, qualidade_id
            ),
            base AS (
                SELECT
                    i.id                                                              AS item_id,
                    i.categoria_id,
                    i.ingles                                                          AS item_ingles,
                    i.portugues                                                       AS item_portugues,
                    i.espanhol                                                        AS item_espanhol,
                    i.frances                                                         AS item_frances,
                    i.id_externo,
                    i.nivel,
                    i.encantamento,
                    q.id                                                              AS qualidade_id,
                    q.ingles                                                          AS qualidade_ingles,
                    q.portugues                                                       AS qualidade_portugues,
                    q.espanhol                                                        AS qualidade_espanhol,
                    q.frances                                                         AS qualidade_frances,
                    c_ordem.id                                                        AS cidade_ordem_id,
                    COALESCE(c_ordem.portugues, c_ordem.ingles, c_ordem.nome)        AS cidade_ordem_pt,
                    c_ordem.ingles                                                    AS cidade_ordem_en,
                    c_ordem.espanhol                                                  AS cidade_ordem_es,
                    c_ordem.frances                                                   AS cidade_ordem_fr,
                    ro.ordem_de_compra                                                AS menor_ordem,
                    c_compra.id                                                       AS cidade_compra_id,
                    COALESCE(c_compra.portugues, c_compra.ingles, c_compra.nome)     AS cidade_compra_pt,
                    c_compra.ingles                                                   AS cidade_compra_en,
                    c_compra.espanhol                                                 AS cidade_compra_es,
                    c_compra.frances                                                  AS cidade_compra_fr,
                    rmv.valor                                                         AS menor_valor,
                    c_bm.id                                                           AS cidade_venda_id,
                    COALESCE(c_bm.portugues, c_bm.ingles, c_bm.nome)                AS cidade_venda_pt,
                    rbm.valor                                                         AS maior_valor,
                    (CAST(rbm.valor AS SIGNED) - CAST(ro.ordem_de_compra AS SIGNED)) AS lucro_ordem,
                    ROUND((CAST(rbm.valor AS SIGNED) - CAST(ro.ordem_de_compra AS SIGNED)) / ro.ordem_de_compra * 100, 2) AS pct_lucro_ordem,
                    (CAST(rbm.valor AS SIGNED) - CAST(rmv.valor AS SIGNED))           AS lucro_direto,
                    ROUND((CAST(rbm.valor AS SIGNED) - CAST(rmv.valor AS SIGNED)) / rmv.valor * 100, 2) AS pct_lucro_direto,
                    s.total_vendidos,
                    s.preco_medio
                FROM ranked_ordem ro
                JOIN ranked_min_valor rmv ON rmv.item_id = ro.item_id AND rmv.qualidade_id = ro.qualidade_id AND rmv.rn = 1
                JOIN ranked_bm_valor  rbm ON rbm.item_id = ro.item_id AND rbm.qualidade_id = ro.qualidade_id AND rbm.rn = 1
                JOIN item_stats s         ON s.item_id = ro.item_id AND s.qualidade_id = ro.qualidade_id
                JOIN itens i              ON i.id = ro.item_id
                JOIN qualidades q         ON q.id = ro.qualidade_id
                JOIN cidades c_ordem      ON c_ordem.id = ro.cidade_id
                JOIN cidades c_compra     ON c_compra.id = rmv.cidade_id
                JOIN cidades c_bm         ON c_bm.id = rbm.cidade_id
                WHERE ro.rn = 1
            )
        ";

        return [$sql, $cteBindings];
    }

    private function buildFilters(
        ?string $busca,
        ?string $categoriaId,
        ?string $qualidadeId,
        ?string $pctMinLucroDireto,
        ?string $qtdMinVendidos
    ): array {
        $parts    = [];
        $bindings = [];

        if ($busca) {
            $term    = "%{$busca}%";
            $parts[] = '(item_ingles LIKE ? OR item_portugues LIKE ? OR item_espanhol LIKE ?)';
            array_push($bindings, $term, $term, $term);
        }
        if ($categoriaId) {
            $ids          = Categoria::descendantIds((int) $categoriaId);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $parts[]      = "categoria_id IN ({$placeholders})";
            array_push($bindings, ...$ids);
        }
        if ($qualidadeId) {
            $parts[]    = 'qualidade_id = ?';
            $bindings[] = (int) $qualidadeId;
        }
        if ($pctMinLucroDireto !== null && $pctMinLucroDireto !== '') {
            $parts[]    = 'pct_lucro_direto >= ?';
            $bindings[] = (float) $pctMinLucroDireto;
        }
        if ($qtdMinVendidos !== null && $qtdMinVendidos !== '') {
            $parts[]    = 'total_vendidos >= ?';
            $bindings[] = (int) $qtdMinVendidos;
        }

        return [$parts, $bindings];
    }
}
