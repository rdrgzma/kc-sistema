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
        Schema::create('apontamento_tempos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('tipo_atividade');
            $table->text('descricao')->nullable();
            $table->string('modalidade');
            $table->string('local')->nullable();
            $table->date('data_atividade')->index();
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apontamento_tempos');
    }
};
