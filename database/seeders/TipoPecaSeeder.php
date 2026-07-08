<?php

namespace Database\Seeders;

use App\Models\TipoPeca;
use Illuminate\Database\Seeder;

class TipoPecaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Petições de Expediente',
            'Contestação',
            'Apelação',
            'CRM/CRO/COREN',
            'Quesitos',
            'Outros',
        ];

        foreach ($tipos as $tipo) {
            TipoPeca::firstOrCreate(['nome' => $tipo]);
        }
    }
}
