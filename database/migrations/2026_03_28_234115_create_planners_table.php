<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // Permite atrelar o planner a um Processo, Cliente, etc.
            $table->nullableMorphs('plannable');

            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // Dono/Criador
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planners');
    }
};
