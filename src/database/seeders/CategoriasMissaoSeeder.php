<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasMissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('categorias_missao')->insert([
            [
                'nome' => 'RESGATE',
                'descricao' => 'Operações de resgate e salvamento',
                'icone' => 'rescue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'DISTRIBUICAO',
                'descricao' => 'Distribuição de mantimentos e suprimentos',
                'icone' => 'truck',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'LIMPEZA',
                'descricao' => 'Limpeza e recuperação de áreas afetadas',
                'icone' => 'broom',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'SAUDE',
                'descricao' => 'Atendimento médico e primeiros socorros',
                'icone' => 'medical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'ABRIGO',
                'descricao' => 'Organização e manutenção de abrigos',
                'icone' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'TRANSPORTE',
                'descricao' => 'Transporte de pessoas e materiais',
                'icone' => 'car',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'COMUNICACAO',
                'descricao' => 'Apoio em comunicação e informação',
                'icone' => 'megaphone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'OUTROS',
                'descricao' => 'Outras atividades de apoio',
                'icone' => 'help',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
