<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itens', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete()->after('id_externo');
        });
    }

    public function down(): void
    {
        Schema::table('itens', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Categoria::class, 'categoria_id');
            $table->dropColumn('categoria_id');
        });
    }
};
