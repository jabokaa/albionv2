<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'itens';

    protected $fillable = [
        'id_externo',
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