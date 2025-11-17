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
        Schema::create('candidaturas_missao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('missao_id')->constrained('missoes')->onDelete('cascade');
            $table->foreignId('voluntario_id')->constrained('voluntarios')->onDelete('cascade');
            $table->string('status', 30)->default('PENDENTE');
            $table->timestamp('dt_candidatura')->useCurrent();
            $table->timestamp('dt_aprovacao')->nullable();
            $table->timestamp('dt_conclusao')->nullable();
            $table->integer('avaliacao')->nullable();
            $table->text('obs_avaliacao')->nullable();
            $table->timestamps();
            
            $table->unique(['missao_id', 'voluntario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidaturas_missao');
    }
};
