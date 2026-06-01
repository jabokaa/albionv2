<?php

namespace App\Http\Controllers;

use App\DTOs\AlbionHistoricoDto;
use App\DTOs\AlbionPrecoDto;
use App\Models\Categoria;
use App\Models\Cidade;
use App\Models\HistoricoItemPreco;
use App\Models\Item;
use App\Models\ItemPreco;
use App\Models\Qualidade;
use App\Services\AlbionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $busca        = $request->input('busca');
        $categoriaId  = $request->input('categoria');
        $encantamento = $request->input('encantamento'); // null = todos; '0' = base

        $categorias = Categoria::orderBy('portugues')->get();

        $itens = Item::with(['categoria', 'receita'])
            ->select(['id', 'id_externo', 'encantamento', 'categoria_id', 'ingles', 'frances', 'espanhol', 'portugues', 'imagem_url'])
            ->when($busca, fn($q) =>
                $q->where('ingles', 'like', "%{$busca}%")
                  ->orWhere('espanhol', 'like', "%{$busca}%")
                  ->orWhere('portugues', 'like', "%{$busca}%")
            )
            ->when($categoriaId, fn($q) =>
                $q->where('categoria_id', $categoriaId)
            )
            ->when($encantamento !== null && $encantamento !== '', fn($q) =>
                $q->where('encantamento', (int) $encantamento)
            )
            ->whereNotNull('imagem_url')
            ->orderBy('ingles')
            ->paginate(96)
            ->withQueryString();

        return view('itens.index', compact('itens', 'categorias', 'busca', 'categoriaId', 'encantamento'));
    }

    public function mercado(int $id): \Illuminate\View\View
    {
        $item = Item::with(['categoria', 'receita', 'precos.cidade', 'precos.qualidade'])
            ->findOrFail($id);

        $precosPorQualidade = $item->precos
            ->sortBy('cidade_id')
            ->groupBy(fn($p) => $p->qualidade_id)
            ->sortKeys();

        return view('itens.mercado', compact('item', 'precosPorQualidade'));
    }

    public function craft(int $id): \Illuminate\View\View
    {
        $item = Item::with([
            'categoria',
            'receita.ingredientes.item.precos.cidade',
            'precos.cidade',
        ])->findOrFail($id);

        abort_if(!$item->receita, 404);

        $receita = $item->receita;

        $ingredientesData = $receita->ingredientes->map(function ($ing) {
            $precos = $ing->item?->precos ?? collect();

            $minOrdem = $precos->filter(fn($p) => $p->ordem_de_compra > 0)
                               ->sortBy('ordem_de_compra')
                               ->first();

            $minValor = $precos->filter(fn($p) => $p->valor > 0)
                               ->sortBy('valor')
                               ->first();

            return (object) [
                'ingrediente' => $ing,
                'item'        => $ing->item,
                'quantidade'  => $ing->quantidade,
                'min_ordem'   => $minOrdem,
                'min_valor'   => $minValor,
            ];
        });

        $maxVenda   = $item->precos->filter(fn($p) => $p->valor > 0)->sortByDesc('valor')->first();

        $totalCusto = $ingredientesData->sum(
            fn($d) => ($d->min_valor?->valor ?? 0) * (float) $d->quantidade
        );
        $totalVenda = $maxVenda?->valor ?? 0;
        $totalLucro = $totalVenda - $totalCusto;

        return view('itens.craft', compact(
            'item', 'receita', 'ingredientesData',
            'maxVenda', 'totalCusto', 'totalVenda', 'totalLucro'
        ));
    }

    public function atualizarPrecos(int $id, AlbionApiService $apiService): \Illuminate\Http\RedirectResponse
    {
        $item = Item::findOrFail($id);

        $cidades      = Cidade::all()->keyBy('ingles');
        $qualidadeIds = Qualidade::pluck('id')->all();
        $nomeCidades  = $cidades->keys()->all();

        try {
            $historicos = $apiService->buscarHistorico([$item->id_externo], $nomeCidades, 1);
            $precos     = $apiService->buscarPrecos([$item->id_externo], 1);
        } catch (\Exception $e) {
            return redirect()->route('itens.mercado', $id)
                ->with('flash_erro', 'Erro ao consultar a API: ' . $e->getMessage());
        }

        $dadosMesclados = $this->mesclarDados($historicos, $precos);

        $processados = 0;

        try {
            DB::transaction(function () use ($dadosMesclados, $item, $cidades, $qualidadeIds, &$processados) {
                foreach ($dadosMesclados as $registro) {
                    $cidade = $cidades->get($registro['city']);
                    if (! $cidade || ! in_array($registro['quality'], $qualidadeIds, true)) {
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
                        ['item_id' => $item->id, 'cidade_id' => $cidade->id, 'qualidade_id' => $registro['quality']],
                        $dadosParaSalvar
                    );

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

                    $processados++;
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('itens.mercado', $id)
                ->with('flash_erro', 'Erro ao salvar os preços: ' . $e->getMessage());
        }

        return redirect()->route('itens.mercado', $id)
            ->with('flash_ok', "{$processados} registros de preço atualizados com sucesso.");
    }

    private function mesclarDados(\Illuminate\Support\Collection $historicos, \Illuminate\Support\Collection $precos): array
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
                $resultado[$chave]['valor']            = $preco->valor;
                $resultado[$chave]['ordem_de_compra']  = $preco->ordemDeCompra;
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
            fn($r) => $r['valor'] > 0 || $r['ordem_de_compra'] > 0
                   || $r['preco_medio'] > 0 || $r['quantidade_itens_vendidos'] > 0
        )->all();
    }
}
