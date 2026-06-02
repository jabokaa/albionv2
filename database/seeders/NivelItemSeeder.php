<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class NivelItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::chunkById(500, function ($itens) {
            foreach ($itens as $item) {
                if (preg_match('/^T([1-8])_/', $item->id_externo, $matches)) {
                    $item->nivel = (int) $matches[1];
                    $item->save();
                }
            }
        });
    }
}
