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
            'Sumaríssimo',
            'Sumário',
            'Especial',
            'Mandado de Segurança',
            'Ação Civil Pública',
            'Ação Popular',
            'Habeas Corpus',
            'Recurso Administrativo',
        ];

        foreach ($procedimentos as $nome) {
            Procedimento::firstOrCreate(['nome' => $nome]);
        }
    }
}
