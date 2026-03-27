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
        Schema::create('lancamento_financeiros', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('tipo'); // 'receita' ou 'despesa'
            $table->string('status'); // 'pendente', 'pago', 'cancelado'
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Polimorfismo: pode estar vinculado a Pessoa ou Processo
            $table->morphs('lancamentable');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamento_financeiros');
    }
};
