<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $busca        = $request->input('busca');
        $categoriaId  = $request->input('categoria');
        $encantamento = $request->input('encantamento'); // null = todos; '0' = base

        $categorias = Categoria::orderBy('portugues')->get();

        $itens = Item::with(['categoria', 'receita'])
            ->select(['id', 'id_externo', 'encantamento', 'categoria_id', 'ingles', 'frances', 'espanhol', 'portugues'])
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
            ->orderBy('ingles')
            ->paginate(96)
            ->withQueryString();

        return view('itens.index', compact('itens', 'categorias', 'busca', 'categoriaId', 'encantamento'));
    }

    public function mercado(int $id): \Illuminate\View\View
    {
        $item = Item::with(['categoria', 'receita', 'precos.cidade', 'precos.qualidade'])
            ->findOrFail($id);

        $precosPorCidade = $item->precos
            ->sortBy('qualidade_id')
            ->groupBy(fn($p) => $p->cidade_id);

        return view('itens.mercado', compact('item', 'precosPorCidade'));
    }
}
