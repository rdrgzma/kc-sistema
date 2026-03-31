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
        Schema::create('categoria_financeiras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tipo')->default('ambos'); // receita, despesa, ambos
            $table->foreignId('escritorio_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_financeiras');
    }
};
