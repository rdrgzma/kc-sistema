<?php

namespace Database\Factories;

use App\Models\Escritorio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Escritorio>
 */
class EscritorioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'cidade' => $this->faker->city,
            'uf' => $this->faker->lexify('??'),
        ];
    }
}
