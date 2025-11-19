<?php

namespace Database\Seeders;

use App\Models\Pais;
use App\Models\Estado;
use App\Models\Cidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalizacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar o país Brasil
        $brasil = Pais::firstOrCreate(
            ['sigla' => 'BRA'],
            [
                'nome_pais' => 'Brasil',
            ]
        );

        // Criar o estado Rio Grande do Sul
        $rs = Estado::firstOrCreate(
            ['uf' => 'RS'],
            [
                'nome_estado' => 'Rio Grande do Sul',
                'pais_id' => $brasil->id,
            ]
        );

        // Criar a cidade Lajeado
        Cidade::firstOrCreate(
            [
                'nome_cidade' => 'Lajeado',
                'estado_id' => $rs->id,
            ],
            [
                'cod_ibge' => '4311403',
            ]
        );
    }
}
