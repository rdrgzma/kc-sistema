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
        Schema::create('indexadores', function (Blueprint $table) {
            $table->id();
            $table->string('categoria');
            $table->string('nome');
            $table->string('sigla');
            $table->integer('codigo_sgs')->nullable();
            $table->enum('tipo', ['INFLACAO', 'TAXA_JUROS', 'TRIBUNAL', 'PARAMETRO_LEGAL']);
            $table->string('fonte');
            $table->boolean('is_composto')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indexadores');
    }
};
