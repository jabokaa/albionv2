<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nome'];

    public function itens()
    {
        return $this->hasMany(Item::class, 'categoria_id');
    }
}
