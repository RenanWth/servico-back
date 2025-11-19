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
        Schema::create('missoes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descricao');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_missao')->onDelete('set null');
            $table->string('local_encontro', 255)->nullable();
            $table->foreignId('cidades_id')->nullable()->constrained('cidades')->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('dt_inicio');
            $table->timestamp('dt_fim')->nullable();
            $table->integer('vagas_totais');
            $table->integer('vagas_preenchidas')->default(0);
            $table->foreignId('admin_criador_id')->constrained('pessoas')->onDelete('restrict');
            $table->string('status', 30)->default('ABERTA');
            $table->timestamp('dt_criacao')->useCurrent();
            $table->timestamp('dt_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missoes');
    }
};
