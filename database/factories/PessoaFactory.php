<?php

namespace Database\Factories;

use App\Models\Escritorio;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['PF', 'PJ']);

        return [
            'tipo' => $tipo,
            'nome_razao' => $tipo === 'PF' ? $this->faker->name : $this->faker->company,
            'cpf_cnpj' => $tipo === 'PF'
                ? $this->faker->numerify('###.###.###-##')
                : $this->faker->numerify('##.###.###/0001-##'),
            'email' => $this->faker->unique()->safeEmail,
            'telefone' => $this->faker->phoneNumber,
            'cep' => $this->faker->postcode,
            'logradouro' => $this->faker->streetName,
            'numero' => $this->faker->buildingNumber,
            'bairro' => $this->faker->word,
            'cidade' => $this->faker->city,
            'estado' => $this->faker->stateAbbr,
            'escritorio_id' => Escritorio::first()?->id ?? Escritorio::factory(),
        ];
    }
}
