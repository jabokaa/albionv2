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
                  ->orWhere('frances', 'like', "%{$busca}%")
                  ->orWhere('espanhol', 'like', "%{$busca}%")
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

    public function busca(Request $request)
    {
        $q = trim($request->input('q', ''));

        $query = Item::with('categoria:id,nome,portugues,ingles,espanhol,frances')
            ->select('id', 'id_externo', 'imagem_url', 'portugues', 'ingles', 'espanhol', 'frances', 'categoria_id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('portugues', 'like', "%{$q}%")
                    ->orWhere('ingles',   'like', "%{$q}%")
                    ->orWhere('frances',  'like', "%{$q}%")
                    ->orWhere('espanhol', 'like', "%{$q}%")
                    ->orWhere('id_externo', 'like', "%{$q}%");
            });
        }

        return response()->json(
            $query->orderBy('ingles')->limit(100)->get()
        );
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
