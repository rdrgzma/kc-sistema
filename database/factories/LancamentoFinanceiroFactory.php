<?php

namespace Database\Factories;

use App\Models\CategoriaFinanceira;
use App\Models\Escritorio;
use App\Models\LancamentoFinanceiro;
use App\Models\Pessoa;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LancamentoFinanceiroFactory extends Factory
{
    protected $model = LancamentoFinanceiro::class;

    public function definition(): array
    {
        $escritorioId = Escritorio::first()?->id ?? Escritorio::factory();
        $tipo = $this->faker->randomElement(['receita', 'despesa']);
        $status = $this->faker->randomElement(['pago', 'pendente', 'cancelado']);

        $lancamentableType = $this->faker->randomElement([Pessoa::class, Processo::class]);
        $lancamentableId = $lancamentableType::where('escritorio_id', $escritorioId)->exists()
            ? $lancamentableType::where('escritorio_id', $escritorioId)->inRandomOrder()->first()->id
            : $lancamentableType::factory(['escritorio_id' => $escritorioId]);

        return [
            'descricao' => $this->faker->sentence(4),
            'valor' => $this->faker->randomFloat(2, 50, 5000),
            'data_vencimento' => $this->faker->dateTimeBetween('-3 months', '+3 months'),
            'data_pagamento' => $status === 'pago' ? $this->faker->dateTimeBetween('-3 months', 'now') : null,
            'tipo' => $tipo,
            'status' => $status,
            'user_id' => User::where('escritorio_id', $escritorioId)->inRandomOrder()->first()?->id ?? User::factory(['escritorio_id' => $escritorioId]),
            'categoria_financeira_id' => CategoriaFinanceira::where('escritorio_id', $escritorioId)->inRandomOrder()->first()?->id ?? CategoriaFinanceira::factory(['escritorio_id' => $escritorioId]),
            'lancamentable_type' => $lancamentableType,
            'lancamentable_id' => $lancamentableId,
            'escritorio_id' => $escritorioId,
        ];
    }
}
