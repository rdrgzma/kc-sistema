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
        Schema::create('pastas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('parent_id')->nullable()->constrained('pastas')->onDelete('cascade');
            $table->morphs('pastable'); // Pessoa ou Processo
            $table->foreignId('escritorio_id')->nullable()->constrained('escritorios');
            $table->timestamps();
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('pasta_id')->after('documentable_id')->nullable()->constrained('pastas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (Schema::hasColumn('documentos', 'pasta_id')) {
                $table->dropForeign(['pasta_id']);
                $table->dropColumn('pasta_id');
            }
        });
        Schema::dropIfExists('pastas');
    }
};
