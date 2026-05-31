<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualidadeSeeder extends Seeder
{
    public function run(): void
    {
        $qualidades = [
            ['nome' => 'normal',       'ingles' => 'Normal',       'frances' => 'Normal',          'espanhol' => 'Normal',           'portugues' => 'Normal'],
            ['nome' => 'good',         'ingles' => 'Good',         'frances' => 'Bon',             'espanhol' => 'Bueno',            'portugues' => 'Bom'],
            ['nome' => 'outstanding',  'ingles' => 'Outstanding',  'frances' => 'Remarquable',     'espanhol' => 'Sobresaliente',    'portugues' => 'Excelente'],
            ['nome' => 'excellent',    'ingles' => 'Excellent',    'frances' => 'Excellent',       'espanhol' => 'Excelente',        'portugues' => 'Excepcional'],
            ['nome' => 'masterpiece',  'ingles' => 'Masterpiece',  'frances' => "Chef-d'œuvre",   'espanhol' => 'Obra Maestra',     'portugues' => 'Obra-prima'],
        ];

        foreach ($qualidades as $qualidade) {
            DB::table('qualidades')->updateOrInsert(
                ['nome' => $qualidade['nome']],
                array_merge($qualidade, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
