<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualidade extends Model
{
    protected $table = 'qualidades';

    protected $fillable = [
        'nome',
        'ingles',
        'frances',
        'espanhol',
        'portugues',
    ];

    public function itemPrecos()
    {
        return $this->hasMany(ItemPreco::class, 'qualidade_id');
    }
}
