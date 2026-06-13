<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSoftDeleteAntigasSeeder extends Seeder
{
    /**
     * Soft-deleta categorias criadas antes de 01/06/2026.
     * Útil para arquivar registros de períodos de desenvolvimento/teste.
     */
    public function run(): void
    {
        $corte = '2026-06-10 00:00:00';

        $total = DB::table('categorias')
            ->whereNull('deleted_at')
            ->where('created_at', '<', $corte)
            ->update(['deleted_at' => now()]);

        $this->command->info("Soft-delete aplicado em {$total} categoria(s) criadas antes de {$corte}.");
    }
}
