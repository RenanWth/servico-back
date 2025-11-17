<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('tipos_item')->insert([
            ['nome' => 'Água Mineral', 'descricao' => 'Garrafas de água potável', 'unidade_medida' => 'litros', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Arroz', 'descricao' => 'Arroz tipo 1', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Feijão', 'descricao' => 'Feijão carioca ou preto', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Macarrão', 'descricao' => 'Macarrão diversos tipos', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Óleo', 'descricao' => 'Óleo de cozinha', 'unidade_medida' => 'litros', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Açúcar', 'descricao' => 'Açúcar refinado', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Sal', 'descricao' => 'Sal refinado', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Café', 'descricao' => 'Café em pó', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Leite em Pó', 'descricao' => 'Leite em pó integral', 'unidade_medida' => 'kg', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Enlatados', 'descricao' => 'Alimentos enlatados diversos', 'unidade_medida' => 'unidades', 'categoria' => 'Alimentos', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Roupas Adulto', 'descricao' => 'Roupas para adultos', 'unidade_medida' => 'peças', 'categoria' => 'Vestuário', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Roupas Infantil', 'descricao' => 'Roupas para crianças', 'unidade_medida' => 'peças', 'categoria' => 'Vestuário', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Calçados', 'descricao' => 'Calçados diversos tamanhos', 'unidade_medida' => 'pares', 'categoria' => 'Vestuário', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Cobertores', 'descricao' => 'Cobertores e mantas', 'unidade_medida' => 'unidades', 'categoria' => 'Vestuário', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Colchões', 'descricao' => 'Colchões diversos tamanhos', 'unidade_medida' => 'unidades', 'categoria' => 'Móveis', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Travesseiros', 'descricao' => 'Travesseiros', 'unidade_medida' => 'unidades', 'categoria' => 'Móveis', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Produtos de Higiene', 'descricao' => 'Sabonete, shampoo, pasta de dente', 'unidade_medida' => 'kits', 'categoria' => 'Higiene', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Fraldas Infantil', 'descricao' => 'Fraldas descartáveis infantis', 'unidade_medida' => 'pacotes', 'categoria' => 'Higiene', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Fraldas Geriátrica', 'descricao' => 'Fraldas geriátricas', 'unidade_medida' => 'pacotes', 'categoria' => 'Higiene', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Papel Higiênico', 'descricao' => 'Papel higiênico', 'unidade_medida' => 'pacotes', 'categoria' => 'Higiene', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Medicamentos', 'descricao' => 'Medicamentos básicos', 'unidade_medida' => 'caixas', 'categoria' => 'Saúde', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Material de Limpeza', 'descricao' => 'Desinfetante, água sanitária, etc', 'unidade_medida' => 'kits', 'categoria' => 'Limpeza', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Materiais Escolares', 'descricao' => 'Cadernos, lápis, canetas', 'unidade_medida' => 'kits', 'categoria' => 'Educação', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Brinquedos', 'descricao' => 'Brinquedos para crianças', 'unidade_medida' => 'unidades', 'categoria' => 'Lazer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
