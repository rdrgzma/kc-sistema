<?php

namespace Database\Seeders;

use App\Models\Sentenca;
use Illuminate\Database\Seeder;

class SentencaSeeder extends Seeder
{
    public function run(): void
    {
        $sentencas = [
            'Procedente',
            'Improcedente',
            'Parcialmente Procedente',
            'Extinto sem Resolução de Mérito',
            'Homologação de Acordo',
            'Aguardando Julgamento',
        ];

        foreach ($sentencas as $nome) {
            Sentenca::firstOrCreate(['nome' => $nome]);
        }
    }
}
