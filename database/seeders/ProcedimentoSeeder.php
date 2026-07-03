<?php

namespace Database\Seeders;

use App\Models\Procedimento;
use Illuminate\Database\Seeder;

class ProcedimentoSeeder extends Seeder
{
    public function run(): void
    {
        $procedimentos = [
            'Ordinário',
            'Sumário',
            'Execução Extra Judicial',
            'Juizados Especiais',
            'Recurso Administrativo',
            'Habeas Corpus',
            'Mandado de Segurança',
            'Outro',
        ];

        foreach ($procedimentos as $procedimento) {
            Procedimento::firstOrCreate(['nome' => $procedimento]);
        }
    }
}
