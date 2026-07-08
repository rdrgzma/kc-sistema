<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessoFactory extends Factory
{
    protected $model = Processo::class;

    public function definition(): array
    {
        $escritorioId = Escritorio::first()?->id ?? Escritorio::factory()->create()->id;

        return [
            'numero_processo' => $this->faker->numerify('#######-##.202#.8.##.####'),
            'pessoa_id' => Pessoa::where('escritorio_id', $escritorioId)->exists()
                ? Pessoa::where('escritorio_id', $escritorioId)->inRandomOrder()->first()->id
                : Pessoa::factory(['escritorio_id' => $escritorioId]),
            'responsavel_id' => User::where('escritorio_id', $escritorioId)->exists()
                ? User::where('escritorio_id', $escritorioId)->inRandomOrder()->first()->id
                : User::factory(['escritorio_id' => $escritorioId]),
            'escritorio_id' => $escritorioId,
            'area_id' => Area::inRandomOrder()->first()?->id ?? Area::create(['nome' => 'Cível'])->id,
            'fase_id' => Fase::inRandomOrder()->first()?->id ?? Fase::create(['nome' => 'Conhecimento'])->id,
            'procedimento_id' => Procedimento::inRandomOrder()->first()?->id ?? Procedimento::create(['nome' => 'Comum'])->id,
            'sentenca_id' => Sentenca::inRandomOrder()->first()?->id ?? Sentenca::create(['nome' => 'Procedente'])->id,
            'economia_gerada' => $this->faker->randomFloat(2, 5000, 500000),
            'perda_estimada' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
