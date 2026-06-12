<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $query = Categoria::with('pai')
            ->orderBy('created_at', 'desc');

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('portugues', 'like', "%{$busca}%")
                  ->orWhere('ingles', 'like', "%{$busca}%");
            });
        }

        $categorias = $query->paginate(30)->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'busca'));
    }

    public function create()
    {
        $pais = Categoria::orderBy('nome')->get();

        return view('admin.categorias.create', compact('pais'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255|unique:categorias,nome',
            'portugues'         => 'nullable|string|max:255',
            'ingles'            => 'nullable|string|max:255',
            'espanhol'          => 'nullable|string|max:255',
            'frances'           => 'nullable|string|max:255',
            'categoria_pai_id'  => 'nullable|exists:categorias,id',
        ]);

        Categoria::create($data);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Categoria $categoria)
    {
        $pais = Categoria::where('id', '!=', $categoria->id)
            ->orderBy('nome')
            ->get()
            ->filter(fn($c) => !$this->isDescendant($categoria, $c->id));

        return view('admin.categorias.edit', compact('categoria', 'pais'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255|unique:categorias,nome,' . $categoria->id,
            'portugues'         => 'nullable|string|max:255',
            'ingles'            => 'nullable|string|max:255',
            'espanhol'          => 'nullable|string|max:255',
            'frances'           => 'nullable|string|max:255',
            'categoria_pai_id'  => 'nullable|exists:categorias,id',
        ]);

        if (!empty($data['categoria_pai_id']) && $this->isDescendant($categoria, $data['categoria_pai_id'])) {
            return back()->withErrors(['categoria_pai_id' => 'Não é possível definir um descendente como pai.'])->withInput();
        }

        $categoria->update($data);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->filhos()->exists()) {
            return back()->with('error', 'Remova ou reassocie as subcategorias antes de excluir.');
        }

        if ($categoria->itens()->exists()) {
            return back()->with('error', 'Há itens associados a esta categoria. Reassocie-os primeiro.');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria excluída.');
    }

    private function isDescendant(Categoria $categoria, int $candidatoId): bool
    {
        $filhos = $categoria->filhos()->pluck('id')->toArray();
        if (in_array($candidatoId, $filhos)) {
            return true;
        }
        foreach ($categoria->filhos as $filho) {
            if ($this->isDescendant($filho, $candidatoId)) {
                return true;
            }
        }
        return false;
    }
}
