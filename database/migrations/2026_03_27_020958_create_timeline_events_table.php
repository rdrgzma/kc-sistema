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
        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->morphs('timelineable'); 
    
             $table->foreignId('user_id')->constrained('users'); // Quem registrou
    
            $table->enum('tipo', ['A', 'J', 'F'])->default('A'); // Administrativo, Judicial, Financeiro
            $table->text('descricao');
            $table->datetime('data_evento');
            
            $table->string('legacy_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
    }
};
