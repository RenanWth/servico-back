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
        Schema::create('necessidades_ponto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ponto_coleta_id')->constrained('pontos_coleta')->onDelete('cascade');
            $table->foreignId('tipo_item_id')->constrained('tipos_item')->onDelete('cascade');
            $table->decimal('quantidade_necessaria', 10, 2);
            $table->decimal('quantidade_recebida', 10, 2)->default(0);
            $table->string('prioridade', 20)->default('MEDIA');
            $table->timestamp('dt_criacao')->useCurrent();
            $table->timestamp('dt_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->unique(['ponto_coleta_id', 'tipo_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('necessidades_ponto');
    }
};
