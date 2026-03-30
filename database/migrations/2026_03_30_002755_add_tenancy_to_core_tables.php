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
        Schema::table('pessoas', function (Blueprint $table) {
            $table->foreignId('escritorio_id')->nullable()->constrained('escritorios')->cascadeOnDelete();
        });

        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('escritorio_id')->nullable()->constrained('escritorios')->cascadeOnDelete();
            $table->foreignId('equipe_id')->nullable()->constrained('equipes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeign(['escritorio_id']);
            $table->dropColumn('escritorio_id');
        });

        Schema::table('processos', function (Blueprint $table) {
            $table->dropForeign(['escritorio_id']);
            $table->dropColumn('escritorio_id');
            $table->dropForeign(['equipe_id']);
            $table->dropColumn('equipe_id');
        });
    }
};
