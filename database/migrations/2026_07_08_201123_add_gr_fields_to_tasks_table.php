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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('acao_gr')->nullable()->after('urgency');
            $table->date('data_solicitacao')->nullable()->after('acao_gr');
            $table->date('data_envio')->nullable()->after('data_solicitacao');
            $table->integer('repeticoes')->default(0)->after('data_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['acao_gr', 'data_solicitacao', 'data_envio', 'repeticoes']);
        });
    }
};
