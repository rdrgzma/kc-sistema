<?php

namespace Database\Seeders;

use App\Models\Sentenca;
use Illuminate\Database\Seeder;

class SentencaSeeder extends Seeder
{
    public function run(): void
    {
        $sentencas = [
            'Acordo',
            'Aguardando Baixa',
            'Arquivado',
            'Improcedente',
            'Parcialmente Procedente',
            'Procedente',
        ];

        foreach ($sentencas as $sentenca) {
            Sentenca::firstOrCreate(['nome' => $sentenca]);
        }
    }
}
