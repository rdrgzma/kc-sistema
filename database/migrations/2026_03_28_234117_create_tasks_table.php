<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bucket_id')->constrained()->cascadeOnDelete();

            // Relacionamento Polimórfico (Pode atrelar a Processo, Pessoa, User, Team)
            $table->nullableMorphs('taskable');

            $table->string('title');
            $table->text('description')->nullable();

            // Responsável
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Prazos e Estimativas
            $table->dateTime('due_date')->nullable(); // Data limite real
            $table->integer('duration_value')->nullable(); // Quantidade (ex: 2, 5, 10)
            $table->string('duration_unit')->nullable(); // Unidade (ex: 'horas', 'dias')

            // Urgência/Prioridade
            $table->string('urgency')->default('normal'); // 'baixa', 'normal', 'alta', 'urgente'

            $table->integer('sort')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
