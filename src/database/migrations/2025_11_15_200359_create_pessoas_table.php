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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo', 200);
            $table->string('cpf', 14)->nullable()->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->string('telefone', 20)->nullable();
            $table->date('dt_nascimento')->nullable();
            $table->string('genero', 20)->nullable();
            $table->foreignId('perfil_id')->constrained('perfis')->onDelete('restrict');
            $table->boolean('ativo')->default(true);
            $table->timestamp('dt_cadastro')->useCurrent();
            $table->timestamp('dt_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
