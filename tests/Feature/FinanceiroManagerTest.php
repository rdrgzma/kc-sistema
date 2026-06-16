<?php

use App\Livewire\Financeiro\FinanceiroManager;
use App\Models\Area;
use App\Models\CategoriaFinanceira;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('can create financeiro record associated with a process model', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $escritorio = Escritorio::create(['nome' => 'K&C Advogados']);
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);
    $categoria = CategoriaFinanceira::create(['nome' => 'Honorários', 'tipo' => 'receita', 'escritorio_id' => $escritorio->id]);

    $processo = Processo::factory()->create([
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
    ]);

    Livewire::test(FinanceiroManager::class, ['model' => $processo])
        ->callTableAction('create', data: [
            'descricao' => 'Pagamento Contrato Teste',
            'valor' => 500,
            'tipo' => 'receita',
            'categoria_financeira_id' => $categoria->id,
            'status' => 'pendente',
            'data_vencimento' => now()->toDateString(),
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('lancamento_financeiros', [
        'descricao' => 'Pagamento Contrato Teste',
        'lancamentable_id' => $processo->id,
        'lancamentable_type' => Processo::class,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $processo->id,
        'tipo' => 'F',
        'descricao' => 'Novo lançamento financeiro registrado: Pagamento Contrato Teste no valor de R$ 500,00.',
    ]);
});
