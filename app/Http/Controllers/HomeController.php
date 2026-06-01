<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $transportes = DB::select("
            WITH
            ranked_min_valor AS (
                SELECT item_id, qualidade_id, cidade_id, valor,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY valor ASC) AS rn
                FROM items_precos
                WHERE valor > 0
                  AND cidade_id NOT IN (SELECT id FROM cidades WHERE ingles = 'Black Market')
            ),
            ranked_max_valor AS (
                SELECT item_id, qualidade_id, cidade_id, valor,
                       ROW_NUMBER() OVER (PARTITION BY item_id, qualidade_id ORDER BY valor DESC) AS rn
                FROM items_precos
                WHERE valor > 0
            ),
            base AS (
                SELECT
                    i.id                                                              AS item_id,
                    i.ingles                                                          AS item_ingles,
                    i.portugues                                                       AS item_portugues,
                    i.espanhol                                                        AS item_espanhol,
                    i.frances                                                         AS item_frances,
                    i.encantamento,
                    COALESCE(c_compra.portugues, c_compra.ingles, c_compra.nome)     AS cidade_compra_pt,
                    c_compra.ingles                                                   AS cidade_compra_en,
                    c_compra.espanhol                                                 AS cidade_compra_es,
                    c_compra.frances                                                  AS cidade_compra_fr,
                    rmv.valor                                                         AS menor_valor,
                    COALESCE(c_venda.portugues, c_venda.ingles, c_venda.nome)        AS cidade_venda_pt,
                    c_venda.ingles                                                    AS cidade_venda_en,
                    c_venda.espanhol                                                  AS cidade_venda_es,
                    c_venda.frances                                                   AS cidade_venda_fr,
                    rxv.valor                                                         AS maior_valor,
                    (CAST(rxv.valor AS SIGNED) - CAST(rmv.valor AS SIGNED))          AS lucro_direto,
                    ROUND(
                        (CAST(rxv.valor AS SIGNED) - CAST(rmv.valor AS SIGNED))
                        / NULLIF(rmv.valor, 0) * 100, 2
                    )                                                                 AS pct_lucro_direto
                FROM ranked_min_valor rmv
                JOIN ranked_max_valor rxv ON rxv.item_id = rmv.item_id
                                         AND rxv.qualidade_id = rmv.qualidade_id
                                         AND rxv.rn = 1
                JOIN itens i          ON i.id = rmv.item_id
                JOIN cidades c_compra ON c_compra.id = rmv.cidade_id
                JOIN cidades c_venda  ON c_venda.id = rxv.cidade_id
                WHERE rmv.rn = 1
            )
            SELECT * FROM base
            WHERE pct_lucro_direto <= 500 AND lucro_direto > 0
            ORDER BY lucro_direto DESC
            LIMIT 10
        ");

        return view('home', compact('transportes'));
    }
}
