<?php

namespace App\Console\Commands;

use App\Models\Ingrediente;
use App\Models\Item;
use App\Models\Receita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportReceitas extends Command
{
    protected $signature = 'receitas:importar';

    protected $description = 'Importa receitas de crafting do Albion Online para todos os itens';

    private const URL_API = 'https://gameinfo.albiononline.com/api/gameinfo/items/%s/data';

    public function handle(): int
    {
        $this->info('Carregando itens base (encantamento = 0)...');

        $itens = Item::where('encantamento', 0)->get();
        $total = $itens->count();

        $this->info("Total de itens a processar: {$total}");
        $this->info('Carregando cache de itens...');

        // Cache completo: id_externo → id (evita N queries por ingrediente)
        $cacheItens = Item::pluck('id', 'id_externo')->all();

        $barra = $this->output->createProgressBar($total);
        $barra->start();

        $processados = 0;
        $semReceita  = 0;
        $erros       = 0;

        foreach ($itens as $item) {
            try {
                $resposta = Http::timeout(15)->get(sprintf(self::URL_API, $item->id_externo));

                if ($resposta->failed()) {
                    $erros++;
                    $barra->advance();
                    Log::error("Falha ao buscar receita para item ID {$item->id_externo}: " . $resposta->status());
                    $this->error("Erro ao buscar receita para item ID {$item->id_externo}: " . $resposta->status());
                    continue;
                }

                $this->info("Processando item ID {$item->id_externo}...");
                $dados = $resposta->json();

                // Receita do item base (encantamento 0)
                $requisitos = $dados['craftingRequirements'] ?? null;
                if ($requisitos && ! empty($requisitos['craftResourceList'])) {
                    $this->info('receita encontrada para item ID ' . $item->id_externo);
                    $this->salvarReceita($item->id, $requisitos, $cacheItens);
                    $processados++;
                } else {
                    $this->info('Sem receita para item ID ' . $item->id_externo);
                    $semReceita++;
                }

                // Receitas dos níveis de encantamento (1 a 4)
                foreach ($dados['enchantments']['enchantments'] ?? [] as $encantamento) {
                    $nivel          = $encantamento['enchantmentLevel'];
                    $requisitosEnc  = $encantamento['craftingRequirements'] ?? null;

                    if (! $requisitosEnc || empty($requisitosEnc['craftResourceList'])) {
                        continue;
                    }

                    $idExternoEnc = $item->id_externo . '@' . $nivel;
                    $itemIdEnc    = $cacheItens[$idExternoEnc] ?? null;

                    if (! $itemIdEnc) {
                        continue;
                    }

                    $this->salvarReceita($itemIdEnc, $requisitosEnc, $cacheItens);
                    $processados++;
                }

                usleep(100_000); // 100ms entre requisições para não sobrecarregar a API
            } catch (\Exception $e) {
                $erros++;
            }

            $barra->advance();
        }

        $barra->finish();
        $this->newLine();
        $this->info("Concluído! Receitas salvas: {$processados} | Sem receita: {$semReceita} | Erros: {$erros}");

        return Command::SUCCESS;
    }

    private function salvarReceita(int $itemId, array $requisitos, array $cacheItens, int $nivel = 0): void
    {
        $receita = Receita::updateOrCreate(
            ['item_id' => $itemId],
            [
                'valor' => $requisitos['silver'] ?? 0,
                'foco'  => $requisitos['craftingFocus'] ?? 0,
            ]
        );

        $receita->ingredientes()->delete();

        $linhas = [];
        foreach ($requisitos['craftResourceList'] as $recurso) {
            $nomeExterno = $recurso['uniqueName'];
            if($nivel > 0) {
                $nomeExterno = $recurso['uniqueName'] . '@' . $nivel;
            }
            $idItem = $cacheItens[$nomeExterno] ?? $cacheItens[$recurso['uniqueName']];

            if (! $idItem) {
                continue;
            }

            $linhas[] = [
                'id_receita' => $receita->id,
                'id_item'    => $idItem,
                'quantidade' => $recurso['count'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($linhas)) {
            Ingrediente::insert($linhas);
        }
    }
}
