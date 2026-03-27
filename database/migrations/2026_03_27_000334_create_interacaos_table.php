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
        Schema::create('interacaos', function (Blueprint $table) {
            $table->id();
            $table->morphs('interactable');
            
            // Quem realizou o atendimento
            $table->foreignId('user_id')->nullable()->constrained('users');
            
            $table->enum('tipo', ['whatsapp', 'telefone', 'email', 'reuniao', 'presencial'])->default('whatsapp');
            $table->string('assunto');
            $table->text('descricao')->nullable();
            $table->dateTime('data_interacao');
            
            // Controle de agenda vs realizado
            $table->enum('status', ['agendada', 'realizada', 'cancelada'])->default('realizada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interacaos');
    }
};
