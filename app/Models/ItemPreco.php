<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPreco extends Model
{
    protected $table = 'items_precos';

    protected $fillable = [
        'item_id',
        'cidade_id',
        'qualidade_id',
        'valor',
        'ordem_de_compra',
        'preco_medio',
        'quantidade_itens_vendidos',
        'data_atualizacao',
    ];

    protected $casts = [
        'data_atualizacao' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function qualidade()
    {
        return $this->belongsTo(Qualidade::class, 'qualidade_id');
    }

    public function historico()
    {
        return $this->hasMany(HistoricoItemPreco::class, 'item_preco_id');
    }
}
