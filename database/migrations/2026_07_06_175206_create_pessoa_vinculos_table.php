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
        Schema::create('pessoa_vinculos', function (Blueprint $table) {
            $table->foreignId('pessoa_fisica_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('pessoa_juridica_id')->constrained('pessoas')->cascadeOnDelete();
            $table->primary(['pessoa_fisica_id', 'pessoa_juridica_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa_vinculos');
    }
};
