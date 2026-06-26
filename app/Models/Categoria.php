<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;
    protected $table = 'categorias';

    protected $fillable = ['nome', 'ingles', 'frances', 'espanhol', 'portugues', 'categoria_pai_id', 'ordem'];

    public function itens()
    {
        return $this->hasMany(Item::class, 'categoria_id');
    }

    public function pai()
    {
        return $this->belongsTo(Categoria::class, 'categoria_pai_id');
    }

    public function filhos()
    {
        return $this->hasMany(Categoria::class, 'categoria_pai_id');
    }

    public function todosFilhos()
    {
        return $this->filhos()->with('todosFilhos');
    }

    public function ancestrais(): array
    {
        $chain = [];
        $current = $this->pai;
        while ($current) {
            array_unshift($chain, $current);
            $current = $current->pai;
        }
        return $chain;
    }

    public static function descendantIds(int $id): array
    {
        $cat = static::with('todosFilhos')->find($id);
        if (! $cat) {
            return [$id];
        }
        $ids = [$id];
        static::collectDescendantIds($cat->todosFilhos, $ids);
        return $ids;
    }

    private static function collectDescendantIds($filhos, array &$ids): void
    {
        foreach ($filhos as $filho) {
            $ids[] = $filho->id;
            static::collectDescendantIds($filho->todosFilhos, $ids);
        }
    }
}
