<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_precos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('itens')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->cascadeOnDelete();
            $table->foreignId('qualidade_id')->constrained('qualidades')->cascadeOnDelete();
            $table->unsignedBigInteger('valor')->default(0);
            $table->unsignedBigInteger('ordem_de_compra')->default(0);
            $table->unsignedBigInteger('preco_medio')->default(0);
            $table->unsignedBigInteger('quantidade_itens_vendidos')->default(0);
            $table->timestamp('data_atualizacao')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'cidade_id', 'qualidade_id']);
        });

        Schema::create('historico_items_precos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_preco_id')->constrained('items_precos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->cascadeOnDelete();
            $table->foreignId('qualidade_id')->constrained('qualidades')->cascadeOnDelete();
            $table->unsignedBigInteger('valor')->default(0);
            $table->unsignedBigInteger('ordem_de_compra')->default(0);
            $table->unsignedBigInteger('preco_medio')->default(0);
            $table->unsignedBigInteger('quantidade_itens_vendidos')->default(0);
            $table->timestamp('data_atualizacao')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('item_id');
            $table->index('cidade_id');
            $table->index('qualidade_id');
            $table->index('data_atualizacao');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_items_precos');
        Schema::dropIfExists('items_precos');
    }
};
