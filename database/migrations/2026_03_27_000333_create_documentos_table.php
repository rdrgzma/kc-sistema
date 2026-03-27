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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // Vincula a Processo ou Pessoa
            
            $table->string('nome_arquivo'); // Nome original (ex: peticao_inicial.pdf)
            $table->string('caminho');      // Caminho no storage (ex: documentos/abc123.pdf)
            $table->string('extensao');     // pdf, docx, jpg
            $table->bigInteger('tamanho');  // em bytes
            
            $table->string('categoria')->nullable(); // Petição, Sentença, Documento Pessoal
            $table->foreignId('user_id')->constrained('users'); // Quem subiu o arquivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
