<?php

namespace Database\Seeders;

use App\Models\Seguradora;
use Illuminate\Database\Seeder;

class SeguradoraSeeder extends Seeder
{
    public function run(): void
    {
        $seguradoras = [
            'Porto Seguro',
            'SulAmérica',
            'Bradesco Seguros',
            'Allianz',
            'Itaú Seguros',
            'Mapfre',
            'Liberty Seguros',
            'HDI Seguros',
        ];

        foreach ($seguradoras as $nome) {
            Seguradora::firstOrCreate(['nome' => $nome]);
        }
    }
}
