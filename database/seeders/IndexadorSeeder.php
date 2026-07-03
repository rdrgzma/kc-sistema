<?php

namespace Database\Seeders;

use App\Models\Indexador;
use Illuminate\Database\Seeder;

class IndexadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indexadores = [
            [
                'categoria' => 'Inflação',
                'nome' => 'Índice Nacional de Preços ao Consumidor',
                'sigla' => 'INPC',
                'codigo_sgs' => 188,
                'tipo' => 'INFLACAO',
                'fonte' => 'BCB_SGS',
                'is_composto' => false,
            ],
            [
                'categoria' => 'Inflação',
                'nome' => 'Índice Nacional de Preços ao Consumidor Amplo Especial',
                'sigla' => 'IPCA-E',
                'codigo_sgs' => 10764,
                'tipo' => 'INFLACAO',
                'fonte' => 'BCB_SGS',
                'is_composto' => false,
            ],
            [
                'categoria' => 'Taxa de Juros',
                'nome' => 'Taxa SELIC',
                'sigla' => 'SELIC',
                'codigo_sgs' => 4390,
                'tipo' => 'TAXA_JUROS',
                'fonte' => 'BCB_SGS',
                'is_composto' => false,
            ],
            [
                'categoria' => 'Taxa de Juros',
                'nome' => 'Taxa Referencial',
                'sigla' => 'TR',
                'codigo_sgs' => 226,
                'tipo' => 'TAXA_JUROS',
                'fonte' => 'BCB_SGS',
                'is_composto' => false,
            ],
            [
                'categoria' => 'Tribunal',
                'nome' => 'Tabela Prática do TJSP - Comum',
                'sigla' => 'TJSP_COMUM',
                'codigo_sgs' => null,
                'tipo' => 'TRIBUNAL',
                'fonte' => 'COMPOSICAO',
                'is_composto' => true,
            ],
            [
                'categoria' => 'Parâmetro Legal',
                'nome' => 'Taxa Legal (Lei 14.905/2024)',
                'sigla' => 'TAXA_LEGAL_14905',
                'codigo_sgs' => null,
                'tipo' => 'PARAMETRO_LEGAL',
                'fonte' => 'REGRA_PHP',
                'is_composto' => false,
            ],
        ];

        foreach ($indexadores as $indexador) {
            Indexador::updateOrCreate(
                ['sigla' => $indexador['sigla']],
                $indexador
            );
        }
    }
}
