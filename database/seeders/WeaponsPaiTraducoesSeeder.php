<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class WeaponsPaiTraducoesSeeder extends Seeder
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
        // Remove o sufixo "-a" para buscar a chave no JSON (ex: "bow-a" → "BOW")
        $tuid = strtoupper(preg_replace('/-a$/', '', $nome));

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

    public function run(): void
    {
        $this->loadJson();

        Categoria::where('nome', 'LIKE', '%-a')
            ->get()
            ->each(function (Categoria $cat) {
                $traducoes = $this->traducoes($cat->nome);
                if (!empty($traducoes)) {
                    $cat->update($traducoes);
                }
            });
    }
}
