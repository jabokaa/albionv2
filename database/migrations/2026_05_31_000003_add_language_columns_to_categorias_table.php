<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('ingles')->nullable()->after('nome');
            $table->string('frances')->nullable()->after('ingles');
            $table->string('espanhol')->nullable()->after('frances');
            $table->string('portugues')->nullable()->after('espanhol');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['ingles', 'frances', 'espanhol', 'portugues']);
        });
    }
};
