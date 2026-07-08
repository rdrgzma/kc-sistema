<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Direito Administrativo',

        ];

        foreach ($areas as $nome) {
            Area::firstOrCreate(['nome' => $nome]);
        }
    }
}
