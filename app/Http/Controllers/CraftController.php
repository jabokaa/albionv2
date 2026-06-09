<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CraftController extends Controller
{
    private const VALID_SORTS = [
        'item_nome'        => 'item_ingles',
        'custo_ordem'      => 'custo_ordem',
        'custo_direto'     => 'custo_direto',
        'maior_venda'      => 'maior_valor',
        'lucro_ordem'      => 'lucro_ordem',
        'pct_lucro_ordem'  => 'pct_lucro_ordem',
        'lucro_direto'     => 'lucro_direto',
        'pct_lucro_direto' => 'pct_lucro_direto',
        'vendidos'         => 'vendidos',
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
        $cidadeCustoId  = $request->input('cidade_custo');
        $cidadeVendaId  = $request->input('cidade_venda');
        $lucroMinOrdem      = $request->input('lucro_min_ordem');
        $lucroMinDireto     = $request->input('lucro_min_direto');
        $pctMinLucroOrdem   = $request->input('pct_min_lucro_ordem');
        $pctMinLucroDireto  = $request->input('pct_min_lucro_direto');
        $vendidosMin        = $request->input('vendidos_min');
        $removerIrreais     = (bool) $request->input('remover_irreais');

        [$whereParts, $outerBindings] = $this->buildFilters(
            $busca, $categoriaId,
            $lucroMinOrdem, $lucroMinDireto,
            $pctMinLucroOrdem, $pctMinLucroDireto,
            $vendidosMin, $removerIrreais
        );

        $whereClause = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

        [$cte, $cteBindings] = $this->cteDefinition(
            $cidadeCustoId ? (int) $cidadeCustoId : null,
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
        $cidades    = Cidade::orderBy('portugues')->get();

        return view('itens.crafting', compact(
            'results', 'total', 'page', 'perPage', 'totalPages',
            'categorias', 'cidades',
            'sortKey', 'sortDir',
            'busca', 'categoriaId',
            'cidadeCustoId', 'cidadeVendaId',
            'lucroMinOrdem', 'lucroMinDireto',
            'pctMinLucroOrdem', 'pctMinLucroDireto',
            'vendidosMin', 'removerIrreais'
        ));
    }

    private function cteDefinition(?int $cidadeCustoId, ?int $cidadeVendaId): array
    {
        $cteBindings = [];
        $custoExtra  = '';
        $vendaExtra  = '';

        if ($cidadeCustoId) {
            $custoExtra     = 'AND ip.cidade_id = ?';
            $cteBindings[]  = $cidadeCustoId; // custo_ordem_cidade
            $cteBindings[]  = $cidadeCustoId; // custo_direto_cidade
        }
        if ($cidadeVendaId) {
            $vendaExtra    = 'AND ip.cidade_id = ?';
            $cteBindings[] = $cidadeVendaId;
        }

        $sql = "
            WITH
            ing_count AS (
                SELECT id_receita, COUNT(*) AS num_ing
                FROM ingredientes
                GROUP BY id_receita
            ),
            custo_ordem_cidade AS (
                SELECT
                    r.item_id         AS item_craftado_id,
                    r.id              AS receita_id,
                    ip.cidade_id,
                    SUM(ip.ordem_de_compra * ing.quantidade) AS custo_total,
                    COUNT(*)          AS ing_com_preco
                FROM receitas r
                JOIN ingredientes ing ON ing.id_receita = r.id
                JOIN items_precos ip  ON ip.item_id = ing.id_item AND ip.qualidade_id = 1
                WHERE ip.ordem_de_compra > 0 {$custoExtra}
                GROUP BY r.item_id, r.id, ip.cidade_id
            ),
            custo_ordem_completo AS (
                SELECT coc.item_craftado_id, coc.cidade_id, coc.custo_total
                FROM custo_ordem_cidade coc
                JOIN ing_count ic ON ic.id_receita = coc.receita_id
                WHERE coc.ing_com_preco = ic.num_ing
            ),
            ranked_custo_ordem AS (
                SELECT *,
                    ROW_NUMBER() OVER (PARTITION BY item_craftado_id ORDER BY custo_total ASC) AS rn
                FROM custo_ordem_completo
            ),
            custo_direto_cidade AS (
                SELECT
                    r.item_id         AS item_craftado_id,
                    r.id              AS receita_id,
                    ip.cidade_id,
                    SUM(ip.valor * ing.quantidade) AS custo_total,
                    COUNT(*)          AS ing_com_preco
                FROM receitas r
                JOIN ingredientes ing ON ing.id_receita = r.id
                JOIN items_precos ip  ON ip.item_id = ing.id_item AND ip.qualidade_id = 1
                WHERE ip.valor > 0 {$custoExtra}
                GROUP BY r.item_id, r.id, ip.cidade_id
            ),
            custo_direto_completo AS (
                SELECT cdc.item_craftado_id, cdc.cidade_id, cdc.custo_total
                FROM custo_direto_cidade cdc
                JOIN ing_count ic ON ic.id_receita = cdc.receita_id
                WHERE cdc.ing_com_preco = ic.num_ing
            ),
            ranked_custo_direto AS (
                SELECT *,
                    ROW_NUMBER() OVER (PARTITION BY item_craftado_id ORDER BY custo_total ASC) AS rn
                FROM custo_direto_completo
            ),
            ranked_venda AS (
                SELECT
                    ip.item_id,
                    ip.cidade_id,
                    ip.valor                     AS maior_valor,
                    ip.quantidade_itens_vendidos AS vendidos,
                    ROW_NUMBER() OVER (PARTITION BY ip.item_id ORDER BY ip.valor DESC) AS rn
                FROM items_precos ip
                WHERE ip.valor > 0 AND ip.qualidade_id = 1 {$vendaExtra}
            ),
            base AS (
                SELECT
                    i.id                                                               AS item_id,
                    i.ingles                                                           AS item_ingles,
                    i.portugues                                                        AS item_portugues,
                    i.espanhol                                                         AS item_espanhol,
                    i.frances                                                          AS item_frances,
                    i.nivel,
                    i.encantamento,
                    i.categoria_id,
                    c_ordem.id                                                         AS cidade_ordem_id,
                    COALESCE(c_ordem.portugues, c_ordem.ingles, c_ordem.nome)         AS cidade_ordem_pt,
                    c_ordem.ingles                                                     AS cidade_ordem_en,
                    c_ordem.espanhol                                                   AS cidade_ordem_es,
                    c_ordem.frances                                                    AS cidade_ordem_fr,
                    rco.custo_total                                                    AS custo_ordem,
                    c_direto.id                                                        AS cidade_direto_id,
                    COALESCE(c_direto.portugues, c_direto.ingles, c_direto.nome)      AS cidade_direto_pt,
                    c_direto.ingles                                                    AS cidade_direto_en,
                    c_direto.espanhol                                                  AS cidade_direto_es,
                    c_direto.frances                                                   AS cidade_direto_fr,
                    rcd.custo_total                                                    AS custo_direto,
                    c_venda.id                                                         AS cidade_venda_id,
                    COALESCE(c_venda.portugues, c_venda.ingles, c_venda.nome)         AS cidade_venda_pt,
                    c_venda.ingles                                                     AS cidade_venda_en,
                    c_venda.espanhol                                                   AS cidade_venda_es,
                    c_venda.frances                                                    AS cidade_venda_fr,
                    rv.maior_valor,
                    rv.vendidos,
                    (CAST(rv.maior_valor AS SIGNED) - CAST(rco.custo_total AS SIGNED)) AS lucro_ordem,
                    ROUND(
                        (CAST(rv.maior_valor AS SIGNED) - CAST(rco.custo_total AS SIGNED))
                        / NULLIF(CAST(rco.custo_total AS SIGNED), 0) * 100, 2
                    )                                                                  AS pct_lucro_ordem,
                    (CAST(rv.maior_valor AS SIGNED) - CAST(rcd.custo_total AS SIGNED)) AS lucro_direto,
                    ROUND(
                        (CAST(rv.maior_valor AS SIGNED) - CAST(rcd.custo_total AS SIGNED))
                        / NULLIF(CAST(rcd.custo_total AS SIGNED), 0) * 100, 2
                    )                                                                  AS pct_lucro_direto
                FROM ranked_custo_ordem rco
                JOIN ranked_custo_direto rcd ON rcd.item_craftado_id = rco.item_craftado_id AND rcd.rn = 1
                JOIN ranked_venda rv          ON rv.item_id = rco.item_craftado_id AND rv.rn = 1
                JOIN itens i                  ON i.id = rco.item_craftado_id
                JOIN cidades c_ordem          ON c_ordem.id = rco.cidade_id
                JOIN cidades c_direto         ON c_direto.id = rcd.cidade_id
                JOIN cidades c_venda          ON c_venda.id = rv.cidade_id
                WHERE rco.rn = 1
            )
        ";

        return [$sql, $cteBindings];
    }

    private function buildFilters(
        ?string $busca,
        ?string $categoriaId,
        ?string $lucroMinOrdem,
        ?string $lucroMinDireto,
        ?string $pctMinLucroOrdem  = null,
        ?string $pctMinLucroDireto = null,
        ?string $vendidosMin       = null,
        bool    $removerIrreais    = false
    ): array {
        $parts    = [];
        $bindings = [];

        if ($busca) {
            $term    = "%{$busca}%";
            $parts[] = '(item_ingles LIKE ? OR item_portugues LIKE ? OR item_espanhol LIKE ?)';
            array_push($bindings, $term, $term, $term);
        }
        if ($categoriaId) {
            $parts[]    = 'categoria_id = ?';
            $bindings[] = (int) $categoriaId;
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
        if ($vendidosMin !== null && $vendidosMin !== '') {
            $parts[]    = 'vendidos >= ?';
            $bindings[] = (int) $vendidosMin;
        }
        if ($removerIrreais) {
            $parts[]    = 'pct_lucro_direto <= ?';
            $bindings[] = 500.0;
        }

        return [$parts, $bindings];
    }
}
