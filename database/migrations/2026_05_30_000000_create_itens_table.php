<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens', function (Blueprint $table) {
            $table->id();
            $table->string('id_externo')->unique();
            $table->string('ingles')->nullable();
            $table->string('alemao')->nullable();
            $table->string('frances')->nullable();
            $table->string('russo')->nullable();
            $table->string('polones')->nullable();
            $table->string('espanhol')->nullable();
            $table->string('portugues')->nullable();
            $table->string('italiano')->nullable();
            $table->string('chines_simplificado')->nullable();
            $table->string('coreano')->nullable();
            $table->string('japones')->nullable();
            $table->string('chines_tradicional')->nullable();
            $table->string('indonesio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens');
    }
};
