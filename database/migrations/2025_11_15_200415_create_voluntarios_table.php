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
        Schema::create('voluntarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->unique()->constrained('pessoas')->onDelete('cascade');
            $table->string('escolaridade', 100)->nullable();
            $table->string('profissao', 150)->nullable();
            $table->text('habilidades')->nullable();
            $table->text('disponibilidade')->nullable();
            $table->text('exp_emergencias')->nullable();
            $table->string('cnh_categoria', 10)->nullable();
            $table->boolean('possui_veiculo')->default(false);
            $table->timestamp('dt_aprovacao')->nullable();
            $table->string('status', 30)->default('PENDENTE');
            $table->text('obs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voluntarios');
    }
};
