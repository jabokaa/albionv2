<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $itens = Item::query()
            ->when($busca, function ($q) use ($busca) {
                $q->where('ingles', 'like', "%{$busca}%")
                  ->orWhere('espanhol', 'like', "%{$busca}%")
                  ->orWhere('portugues', 'like', "%{$busca}%");
            })
            ->orderBy('ingles')
            ->paginate(48)
            ->withQueryString();

        return view('itens.index', compact('itens', 'busca'));
    }
}
