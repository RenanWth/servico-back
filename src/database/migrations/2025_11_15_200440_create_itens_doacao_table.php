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
        Schema::create('itens_doacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doacao_id')->constrained('doacoes')->onDelete('cascade');
            $table->foreignId('tipo_item_id')->constrained('tipos_item')->onDelete('restrict');
            $table->decimal('quantidade', 10, 2);
            $table->text('obs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_doacao');
    }
};
