<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Cidade;
use App\Models\HistoricoItemPreco;
use App\Models\Item;
use App\Models\ItemPreco;
use App\Models\Qualidade;
use App\Services\AlbionApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportarPrecosCommand extends Command
{
    protected $signature   = 'albion:importar-precos';
    protected $description = 'Importa e atualiza os preços dos itens do Albion Online';

    private int $itensProcessados   = 0;
    private int $registrosCriados   = 0;
    private int $registrosAtualizados = 0;
    private int $historicoInseridos = 0;

    public function handle(AlbionApiService $apiService): int
    {
        $inicio = now();

        // Carregar dados de referência uma única vez
        $itens      = Item::all()->keyBy('id_externo');
        $cidades    = Cidade::all()->keyBy('ingles');
        $qualidadeIds = Qualidade::pluck('id')->all();

        $nomeCidades = $cidades->keys()->all();
        $lotes       = $itens->chunk(150);
        $totalLotes  = $lotes->count();

        $this->info("Iniciando importação de preços...");
        $this->info("Itens: {$itens->count()} | Lotes: {$totalLotes} | Cidades: " . count($nomeCidades));
        $this->newLine();

        $bar = $this->output->createProgressBar($totalLotes);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%");
        $bar->start();

        $apiService->setOutputLogger(function (string $msg) use ($bar) {
            $bar->clear();
            $this->warn($msg);
            $bar->display();
        });

        $numeroDeLote = 0;
        foreach ($lotes as $lote) {
            $numeroDeLote++;
            $itemIds = $lote->pluck('id_externo')->all();

            // Buscar histórico
            $historicos = collect();
            try {
                $historicos = $apiService->buscarHistorico($itemIds, $nomeCidades, $numeroDeLote);
            } catch (\Exception $e) {
                $this->logErro($bar, "Erro no histórico (lote {$numeroDeLote}): " . $e->getMessage());
                $bar->advance();
                continue;
            }

            // Buscar preços atuais
            $precos = collect();
            try {
                $precos = $apiService->buscarPrecos($itemIds, $numeroDeLote);
            } catch (\Exception $e) {
                $this->logErro($bar, "Erro nos preços (lote {$numeroDeLote}): " . $e->getMessage());
                $bar->advance();
                continue;
            }

            $dadosMesclados = $this->mesclarDados($historicos, $precos);

            try {
                DB::transaction(function () use (
                    $dadosMesclados, $itens, $cidades, $qualidadeIds, $bar, $numeroDeLote, $totalLotes
                ) {
                    foreach ($dadosMesclados as $registro) {
                        $item = $itens->get($registro['item_id']);
                        if (!$item) {
                            continue;
                        }

                        $cidade = $cidades->get($registro['city']);
                        if (!$cidade) {
                            continue;
                        }

                        if (!in_array($registro['quality'], $qualidadeIds, true)) {
                            continue;
                        }

                        $dadosParaSalvar = [
                            'valor'                     => $registro['valor'],
                            'ordem_de_compra'           => $registro['ordem_de_compra'],
                            'preco_medio'               => $registro['preco_medio'],
                            'quantidade_itens_vendidos' => $registro['quantidade_itens_vendidos'],
                            'data_atualizacao'          => $registro['data_atualizacao'],
                        ];

                        $itemPreco = ItemPreco::updateOrCreate(
                            [
                                'item_id'      => $item->id,
                                'cidade_id'    => $cidade->id,
                                'qualidade_id' => $registro['quality'],
                            ],
                            $dadosParaSalvar
                        );

                        if ($itemPreco->wasRecentlyCreated) {
                            $this->registrosCriados++;
                        } else {
                            $this->registrosAtualizados++;
                        }

                        HistoricoItemPreco::create([
                            'item_preco_id'             => $itemPreco->id,
                            'item_id'                   => $item->id,
                            'cidade_id'                 => $cidade->id,
                            'qualidade_id'              => $registro['quality'],
                            'valor'                     => $registro['valor'],
                            'ordem_de_compra'           => $registro['ordem_de_compra'],
                            'preco_medio'               => $registro['preco_medio'],
                            'quantidade_itens_vendidos' => $registro['quantidade_itens_vendidos'],
                            'data_atualizacao'          => $registro['data_atualizacao'],
                            'created_at'                => now(),
                        ]);
                        $this->historicoInseridos++;

                        $bar->clear();
                        $this->line("Lote {$numeroDeLote}/{$totalLotes} processado");
                        $bar->display();

                        $this->itensProcessados++;
                    }
                });
            } catch (\Exception $e) {
                $this->logErro($bar, "Erro ao persistir lote {$numeroDeLote}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $tempoTotal = $inicio->diff(now())->format('%H:%I:%S');

        $this->info('Importação concluída.');
        $this->newLine();
        $this->line("Itens processados:    {$this->itensProcessados}");
        $this->line("Registros criados:    {$this->registrosCriados}");
        $this->line("Registros atualizados: {$this->registrosAtualizados}");
        $this->line("Históricos inseridos: {$this->historicoInseridos}");
        $this->line("Tempo total:          {$tempoTotal}");

        return Command::SUCCESS;
    }

    /**
     * Mescla dados de histórico e preços atuais por (item_id, cidade, qualidade).
     *
     * @param  Collection<AlbionHistoricoDto>  $historicos
     * @param  Collection<AlbionPrecoDto>      $precos
     * @return array<string, array<string, mixed>>
     */
    private function mesclarDados(Collection $historicos, Collection $precos): array
    {
        $resultado = [];

        foreach ($historicos as $hist) {
            $chave = "{$hist->itemId}|{$hist->location}|{$hist->quality}";
            $resultado[$chave] = [
                'item_id'                   => $hist->itemId,
                'city'                      => $hist->location,
                'quality'                   => $hist->quality,
                'valor'                     => 0,
                'ordem_de_compra'           => 0,
                'preco_medio'               => $hist->precoMedio,
                'quantidade_itens_vendidos' => $hist->quantidadeMediaVendida,
                'data_atualizacao'          => null,
            ];
        }

        foreach ($precos as $preco) {
            $chave = "{$preco->itemId}|{$preco->city}|{$preco->quality}";
            if (isset($resultado[$chave])) {
                $resultado[$chave]['valor']           = $preco->valor;
                $resultado[$chave]['ordem_de_compra'] = $preco->ordemDeCompra;
                $resultado[$chave]['data_atualizacao'] = $preco->dataAtualizacao;
            } else {
                $resultado[$chave] = [
                    'item_id'                   => $preco->itemId,
                    'city'                      => $preco->city,
                    'quality'                   => $preco->quality,
                    'valor'                     => $preco->valor,
                    'ordem_de_compra'           => $preco->ordemDeCompra,
                    'preco_medio'               => 0,
                    'quantidade_itens_vendidos' => 0,
                    'data_atualizacao'          => $preco->dataAtualizacao,
                ];
            }
        }

        return collect($resultado)->filter(
            fn($r) => $r['valor'] > 0
                   || $r['ordem_de_compra'] > 0
                   || $r['preco_medio'] > 0
                   || $r['quantidade_itens_vendidos'] > 0
        )->all();
    }

    private function logErro(ProgressBar $bar, string $mensagem): void
    {
        Log::error($mensagem);
        $bar->clear();
        $this->error($mensagem);
        $bar->display();
    }
}
