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
        if (! Schema::hasColumn('peca_processuals', 'tipo_peca_id')) {
            Schema::table('peca_processuals', function (Blueprint $table) {
                $table->foreignId('tipo_peca_id')->nullable()->constrained('tipo_pecas');
            });
        }

        if (Schema::hasColumn('peca_processuals', 'tipo_peca')) {
            try {
                Schema::table('peca_processuals', function (Blueprint $table) {
                    $table->dropIndex('peca_processuals_tipo_peca_index');
                });
            } catch (Exception $e) {
                // Ignore if index already dropped or doesn't exist
            }

            Schema::table('peca_processuals', function (Blueprint $table) {
                $table->dropColumn('tipo_peca');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('peca_processuals', 'tipo_peca')) {
            Schema::table('peca_processuals', function (Blueprint $table) {
                $table->string('tipo_peca')->nullable();
            });
        }

        if (Schema::hasColumn('peca_processuals', 'tipo_peca_id')) {
            Schema::table('peca_processuals', function (Blueprint $table) {
                $table->dropForeign(['tipo_peca_id']);
                $table->dropColumn('tipo_peca_id');
            });
        }
    }
};
