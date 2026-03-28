<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Direito Previdenciário',
            'Direito Trabalhista',
            'Direito Civil',
            'Direito do Consumidor',
            'Direito Tributário',
            'Direito Penal',
            'Direito de Família',
            'Direito Empresarial',
        ];

        foreach ($areas as $nome) {
            Area::firstOrCreate(['nome' => $nome]);
        }
    }
}
