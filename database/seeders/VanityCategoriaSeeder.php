<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class VanityCategoriaSeeder extends Seeder
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
        $semPrefixo = preg_replace('/^vanity-/', '', $nome);
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
            'category' => 'vanity',
            'subcategories' => [
                [
                    'id' => 'avatar',
                    'subcategories2' => ['journal', 'adventurerchallenge', 'gvgseason', 'anniversary', 'vanity-other'],
                ],
                [
                    'id' => 'avatar',
                    'subcategories2' => ['journal', 'adventurerchallenge', 'gvgseason', 'anniversary', 'other'],
                ],
                [
                    'id' => 'vanity-mounts',
                    'subcategories2' => [
                        'vanity-horse', 'vanity-armoredhorse', 'vanity-ox', 'vanity-direwolf',
                        'vanity-giantstag', 'vanity-cougar', 'vanity-direboar', 'vanity-direbear',
                        'vanity-lizard', 'vanity-donkey', 'vanity-mammoth',
                    ],
                ],
                [
                    'id' => 'vanity-weapons',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-armors',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-head',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-shoes',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-offhands',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-capes',
                    'subcategories2' => [],
                ],
                [
                    'id' => 'vanity-killemotes',
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
