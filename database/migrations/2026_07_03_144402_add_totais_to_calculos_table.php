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
        Schema::table('calculos', function (Blueprint $table) {
            $table->decimal('valor_original', 15, 2)->default(0)->after('parametros');
            $table->decimal('valor_corrigido', 15, 2)->default(0)->after('valor_original');
            $table->decimal('juros_total', 15, 2)->default(0)->after('valor_corrigido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculos', function (Blueprint $table) {
            $table->dropColumn(['valor_original', 'valor_corrigido', 'juros_total']);
        });
    }
};
