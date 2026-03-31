<?php

namespace Database\Seeders;

use App\Models\CategoriaFinanceira;
use App\Models\Escritorio;
use App\Models\LancamentoFinanceiro;
use Illuminate\Database\Seeder;

class FinanceiroSeeder extends Seeder
{
    public function run(): void
    {
        $escritorio = Escritorio::first();

        if (! $escritorio) {
            return;
        }

        // Criando Categorias Essenciais
        $categorias = [
            ['nome' => 'Honorários Contratuais', 'tipo' => 'receita'],
            ['nome' => 'Honorários de Sucumbência', 'tipo' => 'receita'],
            ['nome' => 'Custas Processuais', 'tipo' => 'despesa'],
            ['nome' => 'Diligências', 'tipo' => 'despesa'],
            ['nome' => 'Manutenção Fixa', 'tipo' => 'despesa'],
            ['nome' => 'Aluguel', 'tipo' => 'despesa'],
        ];

        foreach ($categorias as $cat) {
            CategoriaFinanceira::firstOrCreate(
                ['nome' => $cat['nome'], 'escritorio_id' => $escritorio->id],
                ['tipo' => $cat['tipo']]
            );
        }

        // Criando Lançamentos em Lote (Bulk) para testes de dashboard
        LancamentoFinanceiro::factory()->count(50)->create([
            'escritorio_id' => $escritorio->id,
            'status' => 'pago',
        ]);

        LancamentoFinanceiro::factory()->count(30)->create([
            'escritorio_id' => $escritorio->id,
            'status' => 'pendente',
        ]);
    }
}
