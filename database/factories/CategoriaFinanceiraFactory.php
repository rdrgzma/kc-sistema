<?php

namespace Database\Factories;

use App\Models\CategoriaFinanceira;
use App\Models\Escritorio;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFinanceiraFactory extends Factory
{
    protected $model = CategoriaFinanceira::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->randomElement([
                'Honorários de Sucumbência',
                'Honorários Contratuais',
                'Custas Judiciais',
                'Manutenção Escritório',
                'Aluguel',
                'Materiais de Escritório',
                'Consultoria Técnica',
            ]),
            'tipo' => $this->faker->randomElement(['receita', 'despesa', 'ambos']),
            'escritorio_id' => Escritorio::first()?->id ?? Escritorio::factory(),
        ];
    }
}
