<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progressos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Quem registrou

            $table->text('content');
            $table->string('type')->default('comment'); // Tipos: 'comment', 'status_change', 'checklist'
            $table->boolean('is_completed')->nullable(); // Útil se for um item de checklist

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progressos');
    }
};
