<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cidade;
use App\Models\Qualidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrecoController extends Controller
{
    private const VALID_SORTS = [
        'item_nome'                 => 'i.ingles',
        'qualidade_nome'            => 'q.id',
        'cidade_nome'               => 'cidade_pt',
        'ordem_de_compra'           => 'ip.ordem_de_compra',
        'valor'                     => 'ip.valor',
        'preco_medio'               => 'ip.preco_medio',
        'quantidade_itens_vendidos' => 'ip.quantidade_itens_vendidos',
    ];

    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100, 250])
            ? (int) $request->input('per_page')
            : 50;
        $page   = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $perPage;

        $sortKey = $request->input('sort', 'item_nome');
        if (! array_key_exists($sortKey, self::VALID_SORTS)) {
            $sortKey = 'item_nome';
        }
        $sortDir    = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $sortDirSql = strtoupper($sortDir);
        $sortCol    = self::VALID_SORTS[$sortKey];

        $busca          = $request->input('busca');
        $categoriaId    = $request->input('categoria');
        $qualidadeId    = $request->input('qualidade');
        $nivel          = $request->input('nivel');
        $encantamento   = $request->input('encantamento');
        $cidadeId       = $request->input('cidade');
        $valorMin       = $request->input('valor_min');
        $qtdMinVendidos = $request->input('qtd_min_vendidos');

        $query = DB::table('items_precos as ip')
            ->join('itens as i', 'i.id', '=', 'ip.item_id')
            ->join('qualidades as q', 'q.id', '=', 'ip.qualidade_id')
            ->join('cidades as c', 'c.id', '=', 'ip.cidade_id')
            ->when($busca, function ($q) use ($busca) {
                $term = "%{$busca}%";
                $q->where(function ($w) use ($term) {
                    $w->where('i.ingles', 'like', $term)
                      ->orWhere('i.portugues', 'like', $term)
                      ->orWhere('i.espanhol', 'like', $term);
                });
            })
            ->when($categoriaId, fn($q) => $q->whereIn('i.categoria_id', Categoria::descendantIds((int) $categoriaId)))
            ->when($qualidadeId, fn($q) => $q->where('ip.qualidade_id', (int) $qualidadeId))
            ->when($nivel !== null && $nivel !== '', fn($q) => $q->where('i.nivel', (int) $nivel))
            ->when($encantamento !== null && $encantamento !== '', fn($q) => $q->where('i.encantamento', (int) $encantamento))
            ->when($cidadeId, fn($q) => $q->where('ip.cidade_id', (int) $cidadeId))
            ->when($valorMin !== null && $valorMin !== '', fn($q) => $q->where('ip.valor', '>=', (int) $valorMin))
            ->when($qtdMinVendidos !== null && $qtdMinVendidos !== '', fn($q) => $q->where('ip.quantidade_itens_vendidos', '>=', (int) $qtdMinVendidos));

        $total = (clone $query)->count();

        $results = (clone $query)
            ->select([
                'i.id as item_id',
                'i.ingles as item_ingles',
                'i.portugues as item_portugues',
                'i.espanhol as item_espanhol',
                'i.frances as item_frances',
                'i.nivel',
                'i.encantamento',
                'i.categoria_id',
                'q.id as qualidade_id',
                'q.ingles as qualidade_ingles',
                'q.portugues as qualidade_portugues',
                'q.espanhol as qualidade_espanhol',
                'q.frances as qualidade_frances',
                'c.id as cidade_id',
                DB::raw('COALESCE(c.portugues, c.ingles, c.nome) as cidade_pt'),
                'c.ingles as cidade_en',
                'c.espanhol as cidade_es',
                'c.frances as cidade_fr',
                'ip.ordem_de_compra',
                'ip.valor',
                'ip.preco_medio',
                'ip.quantidade_itens_vendidos',
            ])
            ->orderByRaw("{$sortCol} {$sortDirSql}")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $totalPages = max(1, (int) ceil($total / $perPage));

        $categoriasTree = Categoria::whereNull('categoria_pai_id')
            ->with('filhos.filhos')
            ->orderBy('ordem')
            ->orderBy('portugues')
            ->get();
        $qualidades = Qualidade::orderBy('id')->get();
        $cidades    = Cidade::orderBy('portugues')->get();

        return view('itens.precos', compact(
            'results', 'total', 'page', 'perPage', 'totalPages',
            'categoriasTree', 'qualidades', 'cidades',
            'sortKey', 'sortDir',
            'busca', 'categoriaId', 'qualidadeId', 'nivel', 'encantamento',
            'cidadeId', 'valorMin', 'qtdMinVendidos'
        ));
    }
}
