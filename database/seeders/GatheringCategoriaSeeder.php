<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class GatheringCategoriaSeeder extends Seeder
{
    // Mapa nome → @tuid no categorias.json (quando não é strtoupper direto)
    private array $tuidMap = [
        'fiber_gathering_armor' => 'FIBERGATHERER_ARMOR',
        'fiber_gathering_head'  => 'FIBERGATHERER_HELMET',
        'fiber_gathering_shoes' => 'FIBERGATHERER_SHOES',
        'fiber_gathering_capes' => 'GATHERING_CAPES',
        'hide_gathering_armor'  => 'HIDEGATHERER_ARMOR',
        'hide_gathering_head'   => 'HIDEGATHERER_HELMET',
        'hide_gathering_shoes'  => 'HIDEGATHERER_SHOES',
        'hide_gathering_capes'  => 'GATHERING_CAPES',
        'ore_gathering_armor'   => 'OREGATHERER_ARMOR',
        'ore_gathering_head'    => 'OREGATHERER_HELMET',
        'ore_gathering_shoes'   => 'OREGATHERER_SHOES',
        'ore_gathering_capes'   => 'GATHERING_CAPES',
        'rock_gathering_armor'  => 'ROCKGATHERER_ARMOR',
        'rock_gathering_head'   => 'ROCKGATHERER_HELMET',
        'rock_gathering_shoes'  => 'ROCKGATHERER_SHOES',
        'rock_gathering_capes'  => 'GATHERING_CAPES',
        'wood_gathering_armor'  => 'WOODGATHERER_ARMOR',
        'wood_gathering_head'   => 'WOODGATHERER_HELMET',
        'wood_gathering_shoes'  => 'WOODGATHERER_SHOES',
        'wood_gathering_capes'  => 'GATHERING_CAPES',
    ];

    private array $jsonIndex = [];

    private function loadJson(): void
    {
        $path = base_path('categorias.json');
        if (!file_exists($path)) {
            return;
        }

        foreach (json_decode(file_get_contents($path), true) as $entry) {
            $tuid = $entry['@tuid'];
            $tuvs = isset($entry['tuv']['@xml:lang']) ? [$entry['tuv']] : $entry['tuv'];
            $langs = [];
            foreach ($tuvs as $tuv) {
                $langs[$tuv['@xml:lang']] = $tuv['seg'];
            }
            $this->jsonIndex[$tuid] = $langs;
        }
    }

    private function traducoes(string $nome): array
    {
        $tuid = $this->tuidMap[$nome] ?? strtoupper($nome);
        if (!isset($this->jsonIndex[$tuid])) {
            return [];
        }

        $langMap = [
            'EN-US' => 'ingles',
            'FR-FR' => 'frances',
            'ES-ES' => 'espanhol',
            'PT-BR' => 'portugues',
        ];

        $result = [];
        foreach ($this->jsonIndex[$tuid] as $lang => $seg) {
            if (isset($langMap[$lang])) {
                $result[$langMap[$lang]] = $seg;
            }
        }
        return $result;
    }

    private function upsert(string $nome, ?int $paiId = null): Categoria
    {
        $dados = array_merge($this->traducoes($nome), ['categoria_pai_id' => $paiId]);

        $cat = Categoria::where('nome', $nome)->first();
        if ($cat) {
            $cat->update($dados);
            return $cat;
        }

        $cat = Categoria::withTrashed()->where('nome', $nome)->first();
        if ($cat) {
            $cat->restore();
            $cat->update($dados);
            return $cat;
        }

        return Categoria::create(array_merge(['nome' => $nome], $dados));
    }

    public function run(): void
    {
        $this->loadJson();

        $arvore = [
            'category' => 'gathering',
            'subcategories' => [
                [
                    'id' => 'fiber',
                    'subcategories2' => [
                        'sickle',
                        'fiber_gathering_armor',
                        'fiber_gathering_head',
                        'fiber_gathering_shoes',
                        'fiber_gathering_capes',
                    ],
                ],
                [
                    'id' => 'hide',
                    'subcategories2' => [
                        'knifes',
                        'hide_gathering_armor',
                        'hide_gathering_head',
                        'hide_gathering_shoes',
                        'hide_gathering_capes',
                    ],
                ],
                [
                    'id' => 'ore',
                    'subcategories2' => [
                        'picks',
                        'ore_gathering_armor',
                        'ore_gathering_head',
                        'ore_gathering_shoes',
                        'ore_gathering_capes',
                    ],
                ],
                [
                    'id' => 'rock',
                    'subcategories2' => [
                        'hammer',
                        'rock_gathering_armor',
                        'rock_gathering_head',
                        'rock_gathering_shoes',
                        'rock_gathering_capes',
                    ],
                ],
                [
                    'id' => 'wood',
                    'subcategories2' => [
                        'axes',
                        'wood_gathering_armor',
                        'wood_gathering_head',
                        'wood_gathering_shoes',
                        'wood_gathering_capes',
                    ],
                ],
            ],
        ];

        $raiz = $this->upsert($arvore['category']);

        foreach ($arvore['subcategories'] as $sub) {
            $pai = $this->upsert($sub['id'], $raiz->id);
            foreach ($sub['subcategories2'] as $filho) {
                $this->upsert($filho, $pai->id);
            }
        }
    }
}
