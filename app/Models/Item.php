<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'itens';

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    protected $fillable = [
        'id_externo',
        'categoria_id',
        'encantamento',
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