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
        Schema::create('doacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('restrict');
            $table->foreignId('ponto_coleta_id')->constrained('pontos_coleta')->onDelete('restrict');
            $table->timestamp('dt_doacao')->useCurrent();
            $table->timestamp('dt_entrega')->nullable();
            $table->string('status', 30)->default('REGISTRADA');
            $table->text('obs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacoes');
    }
};
