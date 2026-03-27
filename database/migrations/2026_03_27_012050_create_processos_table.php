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
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_processo')->unique();
            
            // Relacionamento com Pessoas
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            
            // Outros campos base do ERD
            $table->string('seguradora_id')->nullable();
            $table->string('area_id')->nullable();
            $table->string('fase_id')->nullable();
            
            // Performance e Mérito (do Aditivo)
            $table->decimal('economia_gerada', 15, 2)->nullable();
            $table->decimal('perda_estimada', 15, 2)->nullable();
            
            // Legado
            $table->string('legacy_id')->nullable()->index();
            $table->string('legacy_table')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
