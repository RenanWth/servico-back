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
        Schema::create('pontos_coleta', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 200);
            $table->text('descricao')->nullable();
            $table->foreignId('cidades_id')->constrained('cidades')->onDelete('restrict');
            $table->string('endereco', 255);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->text('horario_funcionamento')->nullable();
            $table->string('responsavel_nome', 200)->nullable();
            $table->string('responsavel_telefone', 20)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('dt_criacao')->useCurrent();
            $table->foreignId('admin_criador_id')->constrained('pessoas')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pontos_coleta');
    }
};
