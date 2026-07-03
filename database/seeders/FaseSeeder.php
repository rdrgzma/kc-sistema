<?php

namespace Database\Seeders;

use App\Models\Fase;
use Illuminate\Database\Seeder;

class FaseSeeder extends Seeder
{
    public function run(): void
    {
        $fases = [
            ['nome' => 'Instrução', 'descricao' => 'Fase instrutória'],
            ['nome' => 'Sentença', 'descricao' => 'Aguardando ou com sentença proferida'],
            ['nome' => 'Execução', 'descricao' => 'Fase de execução'],
            ['nome' => 'Fase Recursal', 'descricao' => 'Fase de recursos'],
            ['nome' => 'Penhora', 'descricao' => 'Fase de penhora'],
            ['nome' => 'Penhora/Execução', 'descricao' => 'Fase de penhora e execução combinadas'],
        ];

        foreach ($fases as $fase) {
            Fase::firstOrCreate(['nome' => $fase['nome']]);
        }
    }
}
