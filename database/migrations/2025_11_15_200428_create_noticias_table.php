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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->string('subtitulo', 255)->nullable();
            $table->text('conteudo');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_noticia')->onDelete('set null');
            $table->foreignId('admin_autor_id')->constrained('pessoas')->onDelete('restrict');
            $table->boolean('destaque')->default(false);
            $table->string('status', 30)->default('PUBLICADA');
            $table->timestamp('dt_publicacao')->useCurrent();
            $table->timestamp('dt_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->integer('visualizacoes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
