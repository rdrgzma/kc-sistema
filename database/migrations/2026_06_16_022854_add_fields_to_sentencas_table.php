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
        Schema::table('sentencas', function (Blueprint $table) {
            $table->string('classificacao')->nullable()->index();
            $table->string('tipo_decisao')->nullable();
            $table->decimal('valor_economia', 15, 2)->default(0.00);
            $table->decimal('valor_perda', 15, 2)->default(0.00);
            $table->string('status_financeiro')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sentencas', function (Blueprint $table) {
            $table->dropColumn([
                'classificacao',
                'tipo_decisao',
                'valor_economia',
                'valor_perda',
                'status_financeiro',
            ]);
        });
    }
};
