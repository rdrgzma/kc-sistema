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
        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete()->after('sentenca_id');

            // perito & assistente_tecnico referencing new tables
            $table->foreignId('perito_id')->nullable()->constrained('peritos')->nullOnDelete()->after('responsavel_id');
            $table->foreignId('assistente_tecnico_id')->nullable()->constrained('assistentes_tecnicos')->nullOnDelete()->after('perito_id');

            // fase_recursal_id added as nullable index (add FK later if you create a fases_recursais table)
            $table->unsignedBigInteger('fase_recursal_id')->nullable()->index()->after('fase_id');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            if (Schema::hasColumn('processos', 'responsavel_id')) {
                $table->dropForeign(['responsavel_id']);
            }
            if (Schema::hasColumn('processos', 'perito_id')) {
                $table->dropForeign(['perito_id']);
            }
            if (Schema::hasColumn('processos', 'assistente_tecnico_id')) {
                $table->dropForeign(['assistente_tecnico_id']);
            }
            $table->dropColumn(['responsavel_id', 'perito_id', 'assistente_tecnico_id', 'fase_recursal_id']);
        });
    }
};
