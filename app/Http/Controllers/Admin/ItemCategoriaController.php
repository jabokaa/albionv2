<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemCategoriaController extends Controller
{
    public function index(Request $request)
    {
        $busca      = $request->input('busca');
        $categoriaId = $request->input('categoria_id');

        $query = Item::with('categoria')->orderBy('portugues')->orderBy('ingles');

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('portugues', 'like', "%{$busca}%")
                  ->orWhere('ingles', 'like', "%{$busca}%")
                  ->orWhere('id_externo', 'like', "%{$busca}%");
            });
        }

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        $itens      = $query->paginate(50)->withQueryString();
        $categorias = Categoria::orderBy('nome')->get();

        return view('admin.itens.index', compact('itens', 'categorias', 'busca', 'categoriaId'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        $item->update($data);

        return back()->with('success', 'Categoria do item atualizada.');
    }

    public function updateLote(Request $request)
    {
        $data = $request->validate([
            'ids'          => 'required|array',
            'ids.*'        => 'exists:itens,id',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        Item::whereIn('id', $data['ids'])->update(['categoria_id' => $data['categoria_id']]);

        return back()->with('success', count($data['ids']) . ' itens atualizados.');
    }
}
