<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receita extends Model
{
    protected $table = 'receitas';

    protected $fillable = [
        'item_id',
        'valor',
        'foco',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function ingredientes(): HasMany
    {
        return $this->hasMany(Ingrediente::class, 'id_receita');
    }
}
