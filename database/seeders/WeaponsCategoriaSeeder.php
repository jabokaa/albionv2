<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Item;
use Illuminate\Database\Seeder;

class WeaponsCategoriaSeeder extends Seeder
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
        // "weapons_a" não existe no JSON; usa "WEAPONS" como chave
        $keyMap = [
            'weapons_a' => 'WEAPONS',
        ];

        $tuid = $keyMap[$nome] ?? strtoupper($nome);

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

    private function upsert(string $nome, ?int $paiId = null, int $ordem = 0): Categoria
    {
        $dados = array_merge($this->traducoes($nome), ['categoria_pai_id' => $paiId, 'ordem' => $ordem]);

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

    private function atribuirCategoriaAItens(Categoria $cat): void
    {
        if (empty($cat->portugues)) {
            return;
        }

        $tiers = [
            'do Calouro',
            'do Novato',
            'do Iniciante',
            'do Adepto',
            'do Perito',
            'do Mestre',
            'do Grão-mestre',
            'do Ancião',
        ];

        foreach ($tiers as $tier) {
            Item::where('portugues', $cat->portugues . ' ' . $tier)
                ->update(['categoria_id' => $cat->id]);
        }
    }

    public function run(): void
    {
        $this->loadJson();

        $arvore = [
            'category' => 'weapons_a',
            'subcategories' => [
                [
                    'id' => 'bow-a',
                    'subcategories2' => [
                        'bow_bow', 'bow_longbow', 'bow_warbow',
                        'bow_undead', 'bow_hell', 'bow_keeper', 'bow_avalon', 'bow_crystal',
                    ],
                ],
                [
                    'id' => 'crossbow-a',
                    'subcategories2' => [
                        'crossbow_crossbow', 'crossbow_crossbowlarge', 'crossbow_1hcrossbow',
                        'crossbow_undead', 'crossbow_hell', 'crossbow_morgana', 'crossbow_avalon', 'crossbow_crystal',
                    ],
                ],
                [
                    'id' => 'axe-a',
                    'subcategories2' => [
                        'axe_main_axe', 'axe_2h_axe', 'axe_halberd',
                        'axe_morgana', 'axe_hell', 'axe_keeper', 'axe_avalon', 'axe_crystal',
                    ],
                ],
                [
                    'id' => 'dagger-a',
                    'subcategories2' => [
                        'dagger_dagger', 'dagger_daggerpair', 'dagger_clawpair',
                        'dagger_morgana', 'dagger_hell', 'dagger_undead', 'dagger_avalon', 'dagger_crystal',
                    ],
                ],
                [
                    'id' => 'hammer-a',
                    'subcategories2' => [
                        'hammer_main_hammer', 'hammer_2h_hammer', 'hammer_polehammer',
                        'hammer_undead', 'hammer_hell', 'hammer_keeper', 'hammer_avalon', 'hammer_crystal',
                    ],
                ],
                [
                    'id' => 'knuckles-a',
                    'subcategories2' => [
                        'knuckles_set1', 'knuckles_set2', 'knuckles_set3', 'knuckles_gauntlets',
                        'knuckles_keeper', 'knuckles_hell', 'knuckles_morgana', 'knuckles_avalon', 'knuckles_crystal',
                    ],
                ],
                [
                    'id' => 'mace-a',
                    'subcategories2' => [
                        'mace_main_mace', 'mace_2h_mace', 'mace_flail',
                        'mace_keeper', 'mace_hell', 'mace_morgana', 'mace_avalon', 'mace_crystal',
                    ],
                ],
                [
                    'id' => 'quarterstaff-a',
                    'subcategories2' => [
                        'quarterstaff_quarterstaff', 'quarterstaff_ironcladedstaff', 'quarterstaff_doublebladedstaff',
                        'quarterstaff_morgana', 'quarterstaff_hell', 'quarterstaff_keeper', 'quarterstaff_avalon', 'quarterstaff_crystal',
                    ],
                ],
                [
                    'id' => 'spear-a',
                    'subcategories2' => [
                        'spear_main_spear', 'spear_2h_spear', 'spear_glaive',
                        'spear_keeper', 'spear_hell', 'spear_undead', 'spear_avalon', 'spear_crystal',
                    ],
                ],
                [
                    'id' => 'sword-a',
                    'subcategories2' => [
                        'sword_sword', 'sword_claymore', 'sword_dualsword',
                        'sword_morgana', 'sword_hell', 'sword_undead', 'sword_avalon', 'sword_crystal',
                    ],
                ],
                [
                    'id' => 'arcanestaff-a',
                    'subcategories2' => [
                        'arcanestaff_main_arcanestaff', 'arcanestaff_2h_arcanestaff', 'arcanestaff_enigmaticstaff',
                        'arcanestaff_undead', 'arcanestaff_hell', 'arcanestaff_morgana', 'arcanestaff_avalon', 'arcanestaff_crystal',
                    ],
                ],
                [
                    'id' => 'cursestaff-a',
                    'subcategories2' => [
                        'cursestaff_main_cursedstaff', 'cursestaff_2h_cursedstaff', 'cursestaff_demonicstaff',
                        'cursestaff_undead', 'cursestaff_hell', 'cursestaff_morgana', 'cursestaff_avalon', 'cursestaff_crystal',
                    ],
                ],
                [
                    'id' => 'firestaff-a',
                    'subcategories2' => [
                        'firestaff_main_firestaff', 'firestaff_2h_firestaff', 'firestaff_infernostaff',
                        'firestaff_keeper', 'firestaff_hell', 'firestaff_morgana', 'firestaff_avalon', 'firestaff_crystal',
                    ],
                ],
                [
                    'id' => 'froststaff-a',
                    'subcategories2' => [
                        'froststaff_main_froststaff', 'froststaff_2h_froststaff', 'froststaff_glacialstaff',
                        'froststaff_keeper', 'froststaff_hell', 'froststaff_undead', 'froststaff_avalon', 'froststaff_crystal',
                    ],
                ],
                [
                    'id' => 'holystaff-a',
                    'subcategories2' => [
                        'holystaff_main_holystaff', 'holystaff_2h_holystaff', 'holystaff_divinestaff',
                        'holystaff_morgana', 'holystaff_hell', 'holystaff_undead', 'holystaff_avalon', 'holystaff_crystal',
                    ],
                ],
                [
                    'id' => 'naturestaff-a',
                    'subcategories2' => [
                        'naturestaff_main_naturestaff', 'naturestaff_2h_naturestaff', 'naturestaff_wildstaff',
                        'naturestaff_main_keeper', 'naturestaff_hell', 'naturestaff_2h_keeper', 'naturestaff_avalon', 'naturestaff_crystal',
                    ],
                ],
                [
                    'id' => 'shapeshifterstaff-a',
                    'subcategories2' => [
                        'shapeshifterstaff_set1', 'shapeshifterstaff_set2', 'shapeshifterstaff_set3',
                        'shapeshifterstaff_morgana', 'shapeshifterstaff_hell', 'shapeshifterstaff_keeper', 'shapeshifterstaff_avalon', 'shapeshifterstaff_crystal',
                    ],
                ],
                [
                    'id' => 'other',
                    'subcategories2' => [],
                ],
            ],
        ];

        $raiz = $this->upsert($arvore['category']);

        foreach ($arvore['subcategories'] as $subOrdem => $sub) {
            $pai = $this->upsert($sub['id'], $raiz->id, $subOrdem);
            foreach ($sub['subcategories2'] as $filhoOrdem => $filho) {
                $catFilho = $this->upsert($filho, $pai->id, $filhoOrdem);
                $this->atribuirCategoriaAItens($catFilho);
            }
        }
    }
}
