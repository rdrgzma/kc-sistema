<?php

namespace Database\Seeders;

use App\Models\Fase;
use Illuminate\Database\Seeder;

class FaseSeeder extends Seeder
{
    public function run(): void
    {
        $fases = [
            'Pré-processual',
            'Conhecimento - 1º Grau',
            'Recurso - 2º Grau',
            'Recurso Especial / Extraordinário',
            'Execução',
            'Cumprimento de Sentença',
            'Encerrado',
        ];

        foreach ($fases as $nome) {
            Fase::firstOrCreate(['nome' => $nome]);
        }
    }
}
