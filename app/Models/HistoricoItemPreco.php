<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoItemPreco extends Model
{
    protected $table = 'historico_items_precos';

    public $timestamps = false;

    protected $fillable = [
        'item_preco_id',
        'item_id',
        'cidade_id',
        'qualidade_id',
        'valor',
        'ordem_de_compra',
        'preco_medio',
        'quantidade_itens_vendidos',
        'data_atualizacao',
        'created_at',
    ];

    protected $casts = [
        'data_atualizacao' => 'datetime',
        'created_at'       => 'datetime',
    ];

    public function itemPreco()
    {
        return $this->belongsTo(ItemPreco::class, 'item_preco_id');
    }

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
}
