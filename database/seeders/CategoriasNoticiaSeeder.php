<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasNoticiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('categorias_noticia')->insert([
            [
                'nome' => 'ALERTA',
                'descricao' => 'Alertas e avisos importantes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'ATUALIZACAO',
                'descricao' => 'Atualizações sobre a situação',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'SUCESSO',
                'descricao' => 'Histórias de sucesso e superação',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'NECESSIDADE',
                'descricao' => 'Comunicados sobre necessidades',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'EVENTO',
                'descricao' => 'Eventos e atividades programadas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'GERAL',
                'descricao' => 'Notícias gerais',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
