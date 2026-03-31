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
        Schema::table('lancamento_financeiros', function (Blueprint $table) {
            $table->foreignId('categoria_financeira_id')->after('valor')->nullable()->constrained('categoria_financeiras')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamento_financeiros', function (Blueprint $table) {
            $table->dropForeign(['categoria_financeira_id']);
            $table->dropColumn('categoria_financeira_id');
        });
    }
};
