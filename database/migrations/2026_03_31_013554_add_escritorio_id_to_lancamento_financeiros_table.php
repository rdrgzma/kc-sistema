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
            $table->foreignId('escritorio_id')->after('status')->nullable()->constrained('escritorios')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamento_financeiros', function (Blueprint $table) {
            $table->dropForeign(['escritorio_id']);
            $table->dropColumn('escritorio_id');
        });
    }
};
