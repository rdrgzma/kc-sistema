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
        Schema::create('assistentes_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('especialidade_id')->nullable()->constrained('especialidades')->nullOnDelete();
            // optional link to users (nullable)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('legacy_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistentes_tecnicos');
    }
};
