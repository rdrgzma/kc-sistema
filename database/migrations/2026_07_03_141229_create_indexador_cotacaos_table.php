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
        Schema::create('indexador_cotacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indexador_id')->constrained('indexadores')->cascadeOnDelete();
            $table->date('data_referencia');
            $table->decimal('valor', 12, 6);
            $table->timestamps();

            $table->unique(['indexador_id', 'data_referencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indexador_cotacoes');
    }
};
