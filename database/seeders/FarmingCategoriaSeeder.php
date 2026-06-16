<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class FarmingCategoriaSeeder extends Seeder
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
        // Remove o prefixo "farming-" antes de buscar no JSON
        $semPrefixo = preg_replace('/^farming-/', '', $nome);
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
            'category' => 'farming',
            'subcategories' => [
                [
                    'id' => 'farm',
                    'subcategories2' => ['seeds', 'plants'],
                ],
                [
                    'id' => 'herbgarden',
                    'subcategories2' => ['farming-seeds', 'herbs'],
                ],
                [
                    'id' => 'pasture',
                    'subcategories2' => ['babys', 'animals', 'egg', 'milk'],
                ],
                [
                    'id' => 'kennel',
                    'subcategories2' => ['farming-babys', 'farming-animals'],
                ],
                [
                    'id' => 'farmingproducts',
                    'subcategories2' => ['alcohol', 'bread', 'butter', 'flour', 'meat'],
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
