<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class ArtefactsCategoriaSeeder extends Seeder
{
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
        // Remove o prefixo "artefacts-" antes de buscar no JSON
        $semPrefixo = preg_replace('/^artefacts-/', '', $nome);
        $tuid       = strtoupper($semPrefixo);

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
            'category' => 'artefacts',
            'subcategories' => [
                [
                    'id' => 'artefacts-weapons',
                    'subcategories2' => [
                        'artefacts-bow', 'artefacts-crossbow', 'artefacts-axe', 'artefacts-dagger',
                        'artefacts-hammer', 'artefacts-knuckles', 'artefacts-mace', 'artefacts-quarterstaff',
                        'artefacts-spear', 'artefacts-sword', 'artefacts-arcanestaff', 'artefacts-cursestaff',
                        'artefacts-firestaff', 'artefacts-froststaff', 'artefacts-holystaff',
                        'artefacts-naturestaff', 'artefacts-shapeshifterstaff',
                    ],
                ],
                [
                    'id' => 'artefacts-armors',
                    'subcategories2' => ['artefacts-cloth_armor', 'artefacts-leather_armor', 'artefacts-plate_armor'],
                ],
                [
                    'id' => 'artefacts-head',
                    'subcategories2' => ['artefacts-cloth_helmet', 'artefacts-leather_helmet', 'artefacts-plate_helmet'],
                ],
                [
                    'id' => 'artefacts-shoes',
                    'subcategories2' => ['artefacts-cloth_shoes', 'artefacts-leather_shoes', 'artefacts-plate_shoes'],
                ],
                [
                    'id' => 'artefacts-offhands',
                    'subcategories2' => ['artefacts-booktype', 'artefacts-torchtype', 'artefacts-shieldtype'],
                ],
                [
                    'id' => 'artefacts-fragments',
                    'subcategories2' => [
                        'artefacts-runes', 'artefacts-souls', 'artefacts-relics',
                        'artefacts-avalonianshards', 'artefacts-crystalshards',
                    ],
                ],
                [
                    'id' => 'artefacts-capes',
                    'subcategories2' => [
                        'artefacts-capes_bridgewatch', 'artefacts-capes_fortsterling', 'artefacts-capes_lymhurst',
                        'artefacts-capes_martlock', 'artefacts-capes_thetford', 'artefacts-capes_caerleon',
                        'artefacts-capes_brecilien', 'artefacts-capes_heretics', 'artefacts-capes_undead',
                        'artefacts-capes_keeper', 'artefacts-capes_morgana', 'artefacts-capes_demon',
                        'artefacts-capes_avalon', 'artefacts-capes_smuggler',
                    ],
                ],
                [
                    'id' => 'artefacts-favor',
                    'subcategories2' => [],
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
