<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class OtherCategoriaSeeder extends Seeder
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
        $semPrefixo = preg_replace('/^other-/', '', $nome);
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
            'category' => 'other',
            'subcategories' => [
                [
                    'id' => 'lootitem',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'guilds',
                    'subcategories2' => ['hideout', 'siegehammer', 'siegebanners'],
                ],
                [
                    'id' => 'labourers',
                    'subcategories2' => ['journals', 'trophies', 'contracts', 'beds', 'tables'],
                ],
                [
                    'id' => 'tokens',
                    'subcategories2' => ['vanity', 'crystalleague', 'anchors', 'other-other'],
                ],
                [
                    'id' => 'luxurygoods',
                    'subcategories2' => ['bridgewatch', 'fortsterling', 'lymhurst', 'martlock', 'thetford', 'caerleon', 'any'],
                ],
                [
                    'id' => 'maps',
                    'subcategories2' => ['randomdungeons', 'corrupteddungeons', 'hellgates', 'other-other'],
                ],
                [
                    'id' => 'hardcoreexpeditions',
                    'subcategories2' => [
                        'event', 'fishybusiness', 'fistfulofsilver', 'lumbercamp', 'mushroom',
                        'recruitment', 'sewerscrypt', 'stonewars', 'threesisters', 'torturer', 'eternalbattle',
                    ],
                ],
                [
                    'id' => 'questitems',
                    'subcategories2' => ['cities', 'other-other'],
                ],
                [
                    'id' => 'killtrophy',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'trash',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'other-other',
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
