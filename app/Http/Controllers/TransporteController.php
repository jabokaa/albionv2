<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cidade;
use App\Models\Qualidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransporteController extends Controller
{
    private const VALID_SORTS = [
        'item_nome'        => 'item_ingles',
        'qualidade_nome'   => 'qualidade_id',
        'cidade_ordem'     => 'cidade_ordem_pt',
        'menor_ordem'      => 'menor_ordem',
        'cidade_compra'    => 'cidade_compra_pt',
        'menor_valor'      => 'menor_valor',
        'cidade_venda'     => 'cidade_venda_pt',
        'maior_valor'      => 'maior_valor',
        'lucro_ordem'      => 'lucro_ordem',
        'pct_lucro_ordem'  => 'pct_lucro_ordem',
        'lucro_direto'     => 'lucro_direto',
        'pct_lucro_direto' => 'pct_lucro_direto',
        'total_vendidos'   => 'total_vendidos',
        'preco_medio'      => 'preco_medio',
    ];

    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100, 250])
            ? (int) $request->input('per_page')
            : 50;
        $page   = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $perPage;

        $sortKey = $request->input('sort', 'lucro_ordem');
        if (! array_key_exists($sortKey, self::VALID_SORTS)) {
            $sortKey = 'lucro_ordem';
        }
        $sortDir    = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortDirSql = strtoupper($sortDir);
        $sortCol    = self::VALID_SORTS[$sortKey];

        $busca          = $request->input('busca');
        $categoriaId    = $request->input('categoria');
        $qualidadeId    = $request->input('qualidade');
        $cidadeOrdemId  = $request->input('cidade_ordem');
        $cidadeCompraId = $request->input('cidade_compra');
        $cidadeVendaId  = $request->input('cidade_venda');
        $lucroMinOrdem      = $request->input('lucro_min_ordem');
        $lucroMinDireto     = $request->input('lucro_min_direto');
        $pctMinLucroOrdem   = $request->input('pct_min_lucro_ordem');
        $pctMinLucroDireto  = $request->input('pct_min_lucro_direto');
        $qtdMinVendidos     = $request->input('qtd_min_vendidos');
        $removerIrreais     = (bool) $request->input('remover_irreais');

        [$whereParts, $outerBindings] = $this->buildFilters(
            $busca, $categoriaId, $qualidadeId,
            $lucroMinOrdem, $lucroMinDireto,
            $pctMinLucroOrdem, $pctMinLucroDireto,
            $qtdMinVendidos, $removerIrreais
        );

        $whereClause = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

        [$cte, $cteBindings] = $this->cteDefinition(
            $cidadeOrdemId ? (int) $cidadeOrdemId : null,
            $cidadeCompraId ? (int) $cidadeCompraId : null,
            $cidadeVendaId  ? (int) $cidadeVendaId  : null,
        );

        $bindings = array_merge($cteBindings, $outerBindings);

        $total   = (int) DB::selectOne("{$cte} SELECT COUNT(*) AS cnt FROM base {$whereClause}", $bindings)->cnt;
        $results = DB::select(
            "{$cte} SELECT * FROM base {$whereClause} ORDER BY {$sortCol} {$sortDirSql} LIMIT ? OFFSET ?",
            array_merge($bindings, [$perPage, $offset])
        );

        $totalPages = max(1, (int) ceil($total / $perPage));

        $categorias = Categoria::orderBy('portugues')->get();
        $qualidades = Qualidade::orderBy('id')->get();
        $cidades    = Cidade::orderBy('portugues')->get();

        return view('itens.transporte', compact(
            'results', 'total', 'page', 'perPage', 'totalPages',
            'categorias', 'qualidades', 'cidades',
            'sortKey', 'sortDir',
            'busca', 'categoriaId', 'qualidadeId',
            'cidadeOrdemId', 'cidadeCompraId', 'cidadeVendaId',
            'lucroMinOrdem', 'lucroMinDireto',
            'pctMinLucroOrdem', 'pctMinLucroDireto',
            'qtdMinVendidos', 'removerIrreais'
        ));
    }

    /**
     * @return array{0: string, 1: array}  [sql, bindings]
     */
    private function cteDefinition(?int $cidadeOrdemId, ?int $cidadeCompraId, ?int $cidadeVendaId): array
    {
        $cteBindings = [];

        $ordemExtra  = '';
        $compraExtra = '';
        $vendaExtra  = '';

        if ($cidadeOrdemId) {
            $ordemExtra  = 'AND cidade_id = ?';
            $cteBindings[] = $cidadeOrdemId;
        }
        if ($cidadeCompraId) {
            $compraExtra = 'AND cidade_id = ?';
            $cteBindings[] = $cidadeCompraId;
        }
        if ($cidadeVendaId) {
            $vendaExtra  = 'AND cidade_id = ?';
            $cteBindings[] = $cidadeVendaId;
        }

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
            ranked_max_valor AS (
                SELECT item_id, qualidade_id, cidade_id, preco_medio as valor,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY valor DESC) AS rn
                FROM items_precos
                WHERE preco_medio > 0
                  {$vendaExtra}
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
                    c_venda.id                                                        AS cidade_venda_id,
                    COALESCE(c_venda.portugues, c_venda.ingles, c_venda.nome)        AS cidade_venda_pt,
                    c_venda.ingles                                                    AS cidade_venda_en,
                    c_venda.espanhol                                                  AS cidade_venda_es,
                    c_venda.frances                                                   AS cidade_venda_fr,
                    rxv.valor                                                         AS maior_valor,
                    (CAST(rxv.valor AS SIGNED) - CAST(ro.ordem_de_compra AS SIGNED)) AS lucro_ordem,
                    ROUND((CAST(rxv.valor AS SIGNED) - CAST(ro.ordem_de_compra AS SIGNED)) / ro.ordem_de_compra * 100, 2) AS pct_lucro_ordem,
                    (CAST(rxv.valor AS SIGNED) - CAST(rmv.valor AS SIGNED))           AS lucro_direto,
                    ROUND((CAST(rxv.valor AS SIGNED) - CAST(rmv.valor AS SIGNED)) / rmv.valor * 100, 2) AS pct_lucro_direto,
                    s.total_vendidos,
                    s.preco_medio
                FROM ranked_ordem ro
                JOIN ranked_min_valor rmv ON rmv.item_id = ro.item_id AND rmv.qualidade_id = ro.qualidade_id AND rmv.rn = 1
                JOIN ranked_max_valor rxv ON rxv.item_id = ro.item_id AND rxv.qualidade_id = ro.qualidade_id AND rxv.rn = 1
                JOIN item_stats s         ON s.item_id = ro.item_id AND s.qualidade_id = ro.qualidade_id
                JOIN itens i              ON i.id = ro.item_id
                JOIN qualidades q         ON q.id = ro.qualidade_id
                JOIN cidades c_ordem      ON c_ordem.id = ro.cidade_id
                JOIN cidades c_compra     ON c_compra.id = rmv.cidade_id
                JOIN cidades c_venda      ON c_venda.id = rxv.cidade_id
                WHERE ro.rn = 1
            )
        ";

        return [$sql, $cteBindings];
    }

    private function buildFilters(
        ?string $busca,
        ?string $categoriaId,
        ?string $qualidadeId,
        ?string $lucroMinOrdem,
        ?string $lucroMinDireto,
        ?string $pctMinLucroOrdem  = null,
        ?string $pctMinLucroDireto = null,
        ?string $qtdMinVendidos    = null,
        bool    $removerIrreais    = false
    ): array {
        $parts    = [];
        $bindings = [];

        if ($busca) {
            $term     = "%{$busca}%";
            $parts[]  = '(item_ingles LIKE ? OR item_portugues LIKE ? OR item_espanhol LIKE ?)';
            array_push($bindings, $term, $term, $term);
        }
        if ($categoriaId) {
            $parts[]    = 'categoria_id = ?';
            $bindings[] = (int) $categoriaId;
        }
        if ($qualidadeId) {
            $parts[]    = 'qualidade_id = ?';
            $bindings[] = (int) $qualidadeId;
        }
        if ($lucroMinOrdem !== null && $lucroMinOrdem !== '') {
            $parts[]    = 'lucro_ordem >= ?';
            $bindings[] = (int) $lucroMinOrdem;
        }
        if ($lucroMinDireto !== null && $lucroMinDireto !== '') {
            $parts[]    = 'lucro_direto >= ?';
            $bindings[] = (int) $lucroMinDireto;
        }
        if ($pctMinLucroOrdem !== null && $pctMinLucroOrdem !== '') {
            $parts[]    = 'pct_lucro_ordem >= ?';
            $bindings[] = (float) $pctMinLucroOrdem;
        }
        if ($pctMinLucroDireto !== null && $pctMinLucroDireto !== '') {
            $parts[]    = 'pct_lucro_direto >= ?';
            $bindings[] = (float) $pctMinLucroDireto;
        }
        if ($qtdMinVendidos !== null && $qtdMinVendidos !== '') {
            $parts[]    = 'total_vendidos >= ?';
            $bindings[] = (int) $qtdMinVendidos;
        }
        if ($removerIrreais) {
            $parts[]    = 'pct_lucro_direto <= ?';
            $bindings[] = 500.0;
        }

        return [$parts, $bindings];
    }
}
