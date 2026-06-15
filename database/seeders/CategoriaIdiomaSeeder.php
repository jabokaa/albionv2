<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaIdiomaSeeder extends Seeder
{
    private const LANG_MAP = [
        'EN-US' => 'ingles',
        'FR-FR' => 'frances',
        'PT-BR' => 'portugues',
    ];

    public function run(): void
    {
        $jsonPath = base_path('categorias.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('Arquivo categorias.json não encontrado em ' . $jsonPath);
            return;
        }

        $entries = json_decode(file_get_contents($jsonPath), true);

        // Índice reverso: tradução em espanhol → demais idiomas
        $indexPorEspanhol = [];
        foreach ($entries as $entry) {
            $langs = [];
            foreach ($entry['tuv'] as $tuv) {
                $langs[$tuv['@xml:lang']] = $tuv['seg'];
            }
            if (!isset($langs['ES-ES'])) {
                continue;
            }
            $indexPorEspanhol[$langs['ES-ES']] = $langs;
        }

        $categorias = Categoria::whereNotNull('espanhol')->get();
        $atualizadas = 0;

        foreach ($categorias as $categoria) {
            if (!isset($indexPorEspanhol[$categoria->espanhol])) {
                continue;
            }

            $langs = $indexPorEspanhol[$categoria->espanhol];
            $dados = [];
            foreach (self::LANG_MAP as $jsonLang => $campo) {
                if (isset($langs[$jsonLang])) {
                    $dados[$campo] = $langs[$jsonLang];
                }
            }

            if (!empty($dados)) {
                $categoria->update($dados);
                $atualizadas++;
            }
        }

        $this->command->info("Categorias atualizadas: {$atualizadas}");
    }
}
