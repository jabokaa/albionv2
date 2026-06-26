<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Item;
use Illuminate\Database\Seeder;

class WeaponsItemCategorizacaoSeeder extends Seeder
{
    private const TIERS = [
        'do Calouro',
        'do Novato',
        'do Iniciante',
        'do Adepto',
        'do Perito',
        'do Mestre',
        'do Grão-mestre',
        'do Ancião',
    ];

    public function run(): void
    {
        // Busca a raiz weapons_a e todas as categorias netas (folhas)
        $raiz = Categoria::where('nome', 'weapons_a')->first();

        if (!$raiz) {
            $this->command->error('Categoria "weapons_a" não encontrada. Execute WeaponsCategoriaSeeder primeiro.');
            return;
        }

        $grupos = Categoria::where('categoria_pai_id', $raiz->id)->pluck('id');

        $folhas = Categoria::whereIn('categoria_pai_id', $grupos)
            ->whereNotNull('portugues')
            ->where('portugues', '!=', '')
            ->get();

        $this->command->info("Encontradas {$folhas->count()} categorias folha com tradução em português.");

        $totalAtualizado = 0;

        foreach ($folhas as $cat) {
            foreach (self::TIERS as $tier) {
                $nome = $cat->portugues . ' ' . $tier;

                $count = Item::where('portugues', $nome)
                    ->update(['categoria_id' => $cat->id]);

                $totalAtualizado += $count;
            }
        }

        $this->command->info("Total de itens atualizados: {$totalAtualizado}");
    }
}
