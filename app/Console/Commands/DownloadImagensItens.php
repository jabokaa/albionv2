<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadImagensItens extends Command
{
    protected $signature = 'itens:baixar-imagens';

    protected $description = 'Baixa imagens dos itens que ainda não têm caminho local (sem imagem ou com URL externa)';

    private const URL_IMAGEM  = 'https://render.albiononline.com/v1/item/';
    private const PASTA_IMAGENS = 'items';

    public function handle(): int
    {
        $pastaLocal = public_path(self::PASTA_IMAGENS);
        File::ensureDirectoryExists($pastaLocal);

        $itens = Item::whereNull('imagem_url')
            ->orWhere('imagem_url', 'like', 'http%')
            ->get(['id', 'id_externo']);

        $total = $itens->count();

        if ($total === 0) {
            $this->info('Todos os itens já têm imagem local.');
            return Command::SUCCESS;
        }

        $this->info("Itens para baixar: {$total}");

        $barra    = $this->output->createProgressBar($total);
        $barra->start();

        $baixados = 0;
        $falhas   = 0;

        foreach ($itens as $item) {
            $nomeArquivo  = $item->id_externo . '.png';
            $caminhoLocal = $pastaLocal . '/' . $nomeArquivo;
            $caminhoRelativo = self::PASTA_IMAGENS . '/' . $nomeArquivo;

            if (File::exists($caminhoLocal)) {
                $item->imagem_url = $caminhoRelativo;
                $item->save();
                $baixados++;
                $barra->advance();
                continue;
            }

            try {
                $resposta = Http::timeout(10)->get(self::URL_IMAGEM . $nomeArquivo);

                if ($resposta->successful()) {
                    File::put($caminhoLocal, $resposta->body());
                    $item->imagem_url = $caminhoRelativo;
                    $item->save();
                    $baixados++;
                } else {
                    $falhas++;
                }
            } catch (\Exception) {
                $falhas++;
            }

            $barra->advance();
        }

        $barra->finish();
        $this->newLine();
        $this->info("Concluído! Baixados: {$baixados} | Falhas: {$falhas}");

        return Command::SUCCESS;
    }
}
