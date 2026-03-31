<?php

namespace Database\Seeders;

use App\Models\Pasta;
use App\Models\Pessoa;
use App\Models\Processo;
use Illuminate\Database\Seeder;

class PastaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pastasPadrao = ['Documentos Pessoais', 'Contratos', 'Petições', 'Provas'];

        Pessoa::all()->each(function ($pessoa) use ($pastasPadrao) {
            foreach ($pastasPadrao as $nome) {
                Pasta::firstOrCreate([
                    'nome' => $nome,
                    'pastable_id' => $pessoa->id,
                    'pastable_type' => Pessoa::class,
                    'escritorio_id' => $pessoa->escritorio_id,
                ]);
            }
        });

        Processo::all()->each(function ($processo) use ($pastasPadrao) {
            foreach ($pastasPadrao as $nome) {
                Pasta::firstOrCreate([
                    'nome' => $nome,
                    'pastable_id' => $processo->id,
                    'pastable_type' => Processo::class,
                    'escritorio_id' => $processo->escritorio_id,
                ]);
            }
        });
    }
}
