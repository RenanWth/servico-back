<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerfisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('perfis')->insert([
            [
                'nome' => 'CIDADAO',
                'descricao' => 'Cidadão comum que pode visualizar informações e fazer doações',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'VOLUNTARIO',
                'descricao' => 'Voluntário que pode participar de missões e fazer doações',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'ADMIN',
                'descricao' => 'Administrador com permissões completas no sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
