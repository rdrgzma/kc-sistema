<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabela_pratica_regras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indexador_id')->constrained('indexadores')->cascadeOnDelete();
            $table->foreignId('indexador_base_id')->constrained('indexadores')->cascadeOnDelete();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabela_pratica_regras');
    }
};
