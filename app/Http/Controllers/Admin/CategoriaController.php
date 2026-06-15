<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $busca  = $request->input('busca');
        $lixeira = $request->boolean('lixeira');

        $query = $lixeira
            ? Categoria::onlyTrashed()->with('pai')->orderBy('deleted_at', 'desc')
            : Categoria::with('pai')->orderBy('created_at', 'desc');

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('portugues', 'like', "%{$busca}%")
                  ->orWhere('ingles', 'like', "%{$busca}%");
            });
        }

        $categorias = $query->paginate(30)->withQueryString();
        $totalLixeira = Categoria::onlyTrashed()->count();

        return view('admin.categorias.index', compact('categorias', 'busca', 'lixeira', 'totalLixeira'));
    }

    public function duplicate(int $id)
    {
        $categoria = Categoria::with('todosFilhos')->findOrFail($id);

        $this->duplicarCategoria($categoria, $categoria->categoria_pai_id);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria "' . $categoria->nome . '" duplicada com sucesso.');
    }

    private function duplicarCategoria(Categoria $original, ?int $paiId): Categoria
    {
        $nova = Categoria::create([
            'nome'             => $this->nomeUnico($original->nome . '_copy'),
            'portugues'        => $original->portugues,
            'ingles'           => $original->ingles,
            'espanhol'         => $original->espanhol,
            'frances'          => $original->frances,
            'categoria_pai_id' => $paiId,
        ]);

        foreach ($original->filhos as $filho) {
            $this->duplicarCategoria($filho, $nova->id);
        }

        return $nova;
    }

    private function nomeUnico(string $base): string
    {
        if (!Categoria::withTrashed()->where('nome', $base)->exists()) {
            return $base;
        }

        $i = 2;
        while (Categoria::withTrashed()->where('nome', $base . '_' . $i)->exists()) {
            $i++;
        }

        return $base . '_' . $i;
    }

    public function restore(int $id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);
        $categoria->restore();

        return redirect()->route('admin.categorias.index', ['lixeira' => 1])
                         ->with('success', 'Categoria restaurada.');
    }

    public function forceDestroy(int $id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);

        if ($categoria->itens()->withTrashed()->exists()) {
            return back()->with('error', 'Há itens associados. Remova-os antes de excluir permanentemente.');
        }

        $categoria->forceDelete();

        return redirect()->route('admin.categorias.index', ['lixeira' => 1])
                         ->with('success', 'Categoria excluída permanentemente.');
    }

    public function create()
    {
        $pais = $this->paisDisponiveis();

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
            'item_ids'          => 'nullable|array',
            'item_ids.*'        => 'exists:itens,id',
        ]);

        if (!empty($data['categoria_pai_id'])) {
            $pai = Categoria::find($data['categoria_pai_id']);
            if ($pai && $this->profundidade($pai) >= 2) {
                return back()->withErrors(['categoria_pai_id' => 'Não é possível criar mais de 3 níveis (avô → pai → filho).'])->withInput();
            }
        }

        $categoria = Categoria::create($data);

        if (!empty($data['item_ids'])) {
            \App\Models\Item::whereIn('id', $data['item_ids'])
                ->update(['categoria_id' => $categoria->id]);
        }

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Categoria $categoria)
    {
        $pais = $this->paisDisponiveis($categoria->id)
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
            'item_ids'          => 'nullable|array',
            'item_ids.*'        => 'exists:itens,id',
        ]);

        if (!empty($data['categoria_pai_id'])) {
            if ($this->isDescendant($categoria, $data['categoria_pai_id'])) {
                return back()->withErrors(['categoria_pai_id' => 'Não é possível definir um descendente como pai.'])->withInput();
            }
            $pai = Categoria::find($data['categoria_pai_id']);
            if ($pai && $this->profundidade($pai) >= 2) {
                return back()->withErrors(['categoria_pai_id' => 'Não é possível criar mais de 3 níveis (avô → pai → filho).'])->withInput();
            }
        }

        $itemIds = $data['item_ids'] ?? [];
        unset($data['item_ids']);

        \App\Models\Item::where('categoria_id', $categoria->id)
            ->whereNotIn('id', $itemIds ?: [0])
            ->update(['categoria_id' => null]);

        if (!empty($itemIds)) {
            \App\Models\Item::whereIn('id', $itemIds)
                ->update(['categoria_id' => $categoria->id]);
        }

        $categoria->update($data);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Request $request, Categoria $categoria)
    {
        if ($categoria->filhos()->exists()) {
            return back()->with('error', 'Remova ou reassocie as subcategorias antes de excluir.');
        }

        if ($categoria->itens()->exists()) {
            return back()->with('error', 'Há itens associados a esta categoria. Reassocie-os primeiro.');
        }

        $categoria->delete();

        $route = $request->input('_from') === 'tree'
            ? route('admin.categorias.tree')
            : route('admin.categorias.index');

        return redirect($route)->with('success', 'Categoria excluída.');
    }

    public function tree()
    {
        $raizes = Categoria::whereNull('categoria_pai_id')
            ->with([
                'filhos' => fn($q) => $q->withCount('itens')->orderBy('nome'),
                'filhos.filhos' => fn($q) => $q->withCount('itens')->orderBy('nome'),
            ])
            ->withCount('itens')
            ->orderBy('nome')
            ->get();

        return view('admin.categorias.tree', compact('raizes'));
    }

    public function mover(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'categoria_pai_id' => 'nullable|exists:categorias,id',
        ]);

        $novoPaiId = $data['categoria_pai_id'] ?? null;

        if ($novoPaiId !== null) {
            if ($novoPaiId == $categoria->id) {
                return response()->json(['error' => 'Uma categoria não pode ser pai de si mesma.'], 422);
            }
            if ($this->isDescendant($categoria, $novoPaiId)) {
                return response()->json(['error' => 'Não é possível definir um descendente como pai.'], 422);
            }
            $pai = Categoria::find($novoPaiId);
            if ($pai && $this->profundidade($pai) >= 2) {
                return response()->json(['error' => 'Não é possível criar mais de 3 níveis (avô → pai → filho).'], 422);
            }
        }

        $categoria->update(['categoria_pai_id' => $novoPaiId]);

        return response()->json(['success' => true]);
    }

    private function paisDisponiveis(?int $excluirId = null): \Illuminate\Support\Collection
    {
        $query = Categoria::with('pai')->orderBy('nome');
        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }
        // Só podem ser pais categorias de nível 0 (raiz) ou nível 1 (pai)
        return $query->get()->filter(
            fn($c) => is_null($c->categoria_pai_id) || is_null($c->pai?->categoria_pai_id)
        );
    }

    private function profundidade(Categoria $cat): int
    {
        $n = 0;
        $id = $cat->categoria_pai_id;
        while ($id) {
            $n++;
            $id = Categoria::find($id)?->categoria_pai_id;
        }
        return $n;
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
