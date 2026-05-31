<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CidadeSeeder extends Seeder
{
    public function run(): void
    {
        $cidades = [
            ['nome' => 'bridgewatch',    'ingles' => 'Bridgewatch',    'frances' => 'Bridgewatch',    'espanhol' => 'Bridgewatch',    'portugues' => 'Bridgewatch'],
            ['nome' => 'martlock',       'ingles' => 'Martlock',       'frances' => 'Martlock',       'espanhol' => 'Martlock',       'portugues' => 'Martlock'],
            ['nome' => 'lymhurst',       'ingles' => 'Lymhurst',       'frances' => 'Lymhurst',       'espanhol' => 'Lymhurst',       'portugues' => 'Lymhurst'],
            ['nome' => 'fortsterling',   'ingles' => 'Fort Sterling',  'frances' => 'Fort Sterling',  'espanhol' => 'Fort Sterling',  'portugues' => 'Fort Sterling'],
            ['nome' => 'thetford',       'ingles' => 'Thetford',       'frances' => 'Thetford',       'espanhol' => 'Thetford',       'portugues' => 'Thetford'],
            ['nome' => 'caerleon',       'ingles' => 'Caerleon',       'frances' => 'Caerleon',       'espanhol' => 'Caerleon',       'portugues' => 'Caerleon'],
            ['nome' => 'brecilien',      'ingles' => 'Brecilien',      'frances' => 'Brecilien',      'espanhol' => 'Brecilien',      'portugues' => 'Brecilien'],
            ['nome' => 'blackmarket',    'ingles' => 'Black Market',   'frances' => 'Marché Noir',    'espanhol' => 'Mercado Negro',  'portugues' => 'Mercado Negro'],
        ];

        foreach ($cidades as $cidade) {
            DB::table('cidades')->updateOrInsert(
                ['nome' => $cidade['nome']],
                array_merge($cidade, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
