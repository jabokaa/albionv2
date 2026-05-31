<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AlbionHistoricoDto;
use App\DTOs\AlbionPrecoDto;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlbionApiService
{
    private const BASE_URL        = 'https://west.albion-online-data.com/api/v2';
    private const RATE_LIMIT_WAIT = 60;
    private const MAX_RETRIES     = 3;

    private ?\Closure $outputLogger = null;

    public function setOutputLogger(\Closure $logger): static
    {
        $this->outputLogger = $logger;
        return $this;
    }

    /**
     * Busca dados históricos de preços para um lote de itens.
     *
     * @param  string[]  $itemIds
     * @param  string[]  $nomeCidades
     * @return Collection<AlbionHistoricoDto>
     */
    public function buscarHistorico(array $itemIds, array $nomeCidades, int $lote): Collection
    {
        $url = self::BASE_URL
            . '/stats/history/' . implode(',', $itemIds) . '.json'
            . '?locations=' . implode(',', $nomeCidades) . '&time-scale=24';

        $dados = $this->fazerRequisicao($url, $lote);

        return collect($dados)->map(function (array $item) {
            $dataArray = collect($item['data'] ?? []);

            $precoMedio = (int) round(
                $dataArray->filter(fn($d) => ($d['avg_price'] ?? 0) > 0)->avg('avg_price') ?? 0
            );
            $quantidadeMedia = (int) round(
                $dataArray->filter(fn($d) => ($d['item_count'] ?? 0) > 0)->avg('item_count') ?? 0
            );

            $dataAtualizacao = $dataArray
                ->pluck('timestamp')
                ->filter()
                ->map(fn($ts) => Carbon::parse($ts))
                ->max();

            return new AlbionHistoricoDto(
                itemId:                 $item['item_id'],
                location:               $item['location'],
                quality:                (int) ($item['quality'] ?? 1),
                precoMedio:             $precoMedio,
                quantidadeMediaVendida: $quantidadeMedia,
                dataAtualizacao:        $dataAtualizacao instanceof Carbon ? $dataAtualizacao : null,
            );
        });
    }

    /**
     * Busca preços atuais para um lote de itens.
     *
     * @param  string[]  $itemIds
     * @return Collection<AlbionPrecoDto>
     */
    public function buscarPrecos(array $itemIds, int $lote): Collection
    {
        $url = self::BASE_URL . '/stats/prices/' . implode(',', $itemIds) . '.json';

        $dados = $this->fazerRequisicao($url, $lote);

        return collect($dados)->map(function (array $item) {
            $dataVenda  = $this->parsearData($item['sell_price_min_date'] ?? null);
            $dataCompra = $this->parsearData($item['buy_price_max_date'] ?? null);

            $dataAtualizacao = match (true) {
                $dataVenda !== null && $dataCompra !== null => $dataVenda->gt($dataCompra) ? $dataVenda : $dataCompra,
                $dataVenda !== null                        => $dataVenda,
                $dataCompra !== null                       => $dataCompra,
                default                                    => null,
            };

            return new AlbionPrecoDto(
                itemId:        $item['item_id'],
                city:          $item['city'],
                quality:       (int) ($item['quality'] ?? 1),
                valor:         (int) ($item['sell_price_min'] ?? 0),
                ordemDeCompra: (int) ($item['buy_price_max'] ?? 0),
                dataAtualizacao: $dataAtualizacao,
            );
        });
    }

    private function parsearData(?string $data): ?Carbon
    {
        if (!$data || str_starts_with($data, '0001-')) {
            return null;
        }
        try {
            return Carbon::parse($data);
        } catch (\Exception) {
            return null;
        }
    }

    private function fazerRequisicao(string $url, int $lote): array
    {
        $tentativas = 0;

        while (true) {
            try {
                $response = Http::timeout(30)->get($url);

                if ($response->status() === 429) {
                    $this->log("Rate limit atingido.");
                    $this->log("Aguardando " . self::RATE_LIMIT_WAIT . " segundos para continuar...");
                    sleep(self::RATE_LIMIT_WAIT);
                    $this->log("Retomando processamento do lote {$lote}.");
                    continue;
                }

                if ($response->serverError()) {
                    $tentativas++;
                    if ($tentativas >= self::MAX_RETRIES) {
                        throw new \RuntimeException(
                            "Erro {$response->status()} após " . self::MAX_RETRIES . " tentativas. URL: {$url}"
                        );
                    }
                    $this->log("Erro {$response->status()} (tentativa {$tentativas}/" . self::MAX_RETRIES . "). Aguardando 5s...");
                    sleep(5);
                    continue;
                }

                if ($response->failed()) {
                    throw new \RuntimeException(
                        "Erro ao requisitar API: HTTP {$response->status()}. URL: {$url}"
                    );
                }

                return $response->json() ?? [];

            } catch (\RuntimeException $e) {
                throw $e;
            } catch (\Exception $e) {
                $tentativas++;
                if ($tentativas >= self::MAX_RETRIES) {
                    throw new \RuntimeException(
                        "Erro de conexão após " . self::MAX_RETRIES . " tentativas: " . $e->getMessage()
                    );
                }
                $this->log("Erro de conexão (tentativa {$tentativas}/" . self::MAX_RETRIES . "): {$e->getMessage()}. Aguardando 5s...");
                sleep(5);
            }
        }
    }

    private function log(string $message): void
    {
        Log::info($message);

        if ($this->outputLogger) {
            ($this->outputLogger)($message);
        }
    }
}
