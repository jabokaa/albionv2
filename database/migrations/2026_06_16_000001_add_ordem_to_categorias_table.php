<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedSmallInteger('ordem')->default(0)->after('categoria_pai_id');
        });

        // Inicializa ordem sequencial para categorias raiz existentes
        $raizes = DB::table('categorias')
            ->whereNull('categoria_pai_id')
            ->whereNull('deleted_at')
            ->orderBy('nome')
            ->pluck('id');

        foreach ($raizes as $i => $id) {
            DB::table('categorias')->where('id', $id)->update(['ordem' => $i]);
        }
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('ordem');
        });
    }
};
