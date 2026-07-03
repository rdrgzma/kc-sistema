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
        Schema::create('calculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->string('titulo');
            $table->date('data_atualizacao');
            $table->foreignId('indexador_id')->constrained('indexadores');
            $table->json('parametros')->nullable();
            $table->decimal('valor_final', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculos');
    }
};
