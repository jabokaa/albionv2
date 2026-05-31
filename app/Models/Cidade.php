<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidades';

    protected $fillable = [
        'nome',
        'ingles',
        'frances',
        'espanhol',
        'portugues',
    ];

    public function itemPrecos()
    {
        return $this->hasMany(ItemPreco::class, 'cidade_id');
    }
}
