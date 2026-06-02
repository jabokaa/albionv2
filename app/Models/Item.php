<?php

namespace App\Models;

use App\Models\Receita;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'itens';

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function receita()
    {
        return $this->hasOne(Receita::class, 'item_id');
    }

    public function precos()
    {
        return $this->hasMany(ItemPreco::class, 'item_id');
    }

    protected $fillable = [
        'id_externo',
        'categoria_id',
        'encantamento',
        'nivel',
        'ingles',
        'alemao',
        'frances',
        'russo',
        'polones',
        'espanhol',
        'portugues',
        'italiano',
        'chines_simplificado',
        'coreano',
        'japones',
        'chines_tradicional',
        'indonesio',
    ];
}