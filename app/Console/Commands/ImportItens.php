<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportItens extends Command
{
    protected $signature = 'itens:importar';

    protected $description = 'Importa itens do Albion Online e salva na tabela itens';

    private const URL_JSON = 'https://raw.githubusercontent.com/broderickhyman/ao-bin-dumps/refs/heads/master/formatted/items.json';

    public function handle(): int
    {
        $this->info('Buscando itens...');

        $resposta = Http::timeout(60)->get(self::URL_JSON);

        if ($resposta->failed()) {
            $this->error('Falha ao buscar o JSON: ' . $resposta->status());
            return Command::FAILURE;
        }

        $itens = $resposta->json();
        $total = count($itens);

        $this->info("Total de itens encontrados: {$total}");

        $barra = $this->output->createProgressBar($total);
        $barra->start();

        $lote = [];
        $inseridos = 0;
        $ignorados = 0;

        foreach ($itens as $item) {
            $nomeUnico = $item['UniqueName'] ?? null;

            if (! $nomeUnico) {
                $ignorados++;
                $barra->advance();
                continue;
            }

            $nomes = $item['LocalizedNames'] ?? [];

            $lote[] = [
                'id_externo'          => $nomeUnico,
                'ingles'              => $nomes['EN-US'] ?? null,
                'alemao'              => $nomes['DE-DE'] ?? null,
                'frances'             => $nomes['FR-FR'] ?? null,
                'russo'               => $nomes['RU-RU'] ?? null,
                'polones'             => $nomes['PL-PL'] ?? null,
                'espanhol'            => $nomes['ES-ES'] ?? null,
                'portugues'           => $nomes['PT-BR'] ?? null,
                'italiano'            => $nomes['IT-IT'] ?? null,
                'chines_simplificado' => $nomes['ZH-CN'] ?? null,
                'coreano'             => $nomes['KO-KR'] ?? null,
                'japones'             => $nomes['JA-JP'] ?? null,
                'chines_tradicional'  => $nomes['ZH-TW'] ?? null,
                'indonesio'           => $nomes['ID-ID'] ?? null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $inseridos++;

            if (count($lote) === 500) {
                $this->salvarLote($lote);
                $lote = [];
            }

            $barra->advance();
        }

        if (! empty($lote)) {
            $this->salvarLote($lote);
        }

        $barra->finish();
        $this->newLine();
        $this->info("Concluído! Inseridos/atualizados: {$inseridos} | Ignorados (sem nome único): {$ignorados}");

        return Command::SUCCESS;
    }

    private function salvarLote(array $lote): void
    {
        $colunas = [
            'ingles', 'alemao', 'frances', 'russo', 'polones',
            'espanhol', 'portugues', 'italiano', 'chines_simplificado',
            'coreano', 'japones', 'chines_tradicional', 'indonesio',
            'updated_at',
        ];

        DB::table('itens')->upsert($lote, ['id_externo'], $colunas);
    }
}
