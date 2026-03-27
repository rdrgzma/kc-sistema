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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['PF', 'PJ'])->default('PF');
            $table->string('nome_razao');
            $table->string('cpf_cnpj')->unique()->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            
            // Campos de Endereço base
            $table->string('cep', 10)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();

            // Rastreabilidade do Legado (K&C)
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
        Schema::dropIfExists('pessoas');
    }
};
