<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';

    protected $fillable = [
        'id_receita',
        'id_item',
        'quantidade',
    ];

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class, 'id_receita');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
