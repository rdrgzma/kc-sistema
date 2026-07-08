<?php

namespace Database\Seeders;

use App\Models\Fase;
use Illuminate\Database\Seeder;

class FaseSeeder extends Seeder
{
    public function run(): void
    {
        $fases = [
            ['nome' => 'Acordo', 'descricao' => 'Fase de Acordo'],
            ['nome' => 'Aguardando Baixa', 'descricao' => 'Fase Aguardando Baixa'],
            ['nome' => 'Arquivado', 'descricao' => ' Processo Arquivada'],
            ['nome' => 'Execução', 'descricao' => 'Fase de execução'],
            ['nome' => 'Fase Recursal', 'descricao' => 'Fase de recursos'],
            ['nome' => 'Instrução', 'descricao' => 'Fase de instrutória'],
            ['nome' => 'Penhora', 'descricao' => 'Fase de penhora'],
            ['nome' => 'Sentença', 'descricao' => 'Fase Sentença'],
        ];

        foreach ($fases as $fase) {
            Fase::firstOrCreate(['nome' => $fase['nome']]);
        }
    }
}
