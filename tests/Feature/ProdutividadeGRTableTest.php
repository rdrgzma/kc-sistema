<?php

use App\Enums\AcaoGR;
use App\Livewire\Dashboard\ProdutividadeGRTable;
use App\Models\Bucket;
use App\Models\PecaProcessual;
use App\Models\Pessoa;
use App\Models\Planner;
use App\Models\Task;
use App\Models\TipoPeca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('ProdutividadeGRTable can render and display GR tasks with their details', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Planner GR Test',
        'user_id' => $user->id,
        'plannable_id' => $user->id,
        'plannable_type' => User::class,
    ]);

    $bucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'A Fazer',
    ]);

    $client = Pessoa::create([
        'nome_razao' => 'Cliente GR Importante',
        'tipo' => 'Fisica',
    ]);

    $tipoDocumento = TipoPeca::create([
        'nome' => 'Relatório Diário GR',
    ]);

    // Create a task without GR fields
    $taskNormal = Task::create([
        'title' => 'Tarefa Comum',
        'bucket_id' => $bucket->id,
    ]);

    // Create a task with GR fields populated
    $taskGR = Task::create([
        'title' => 'Tarefa de Análise GR',
        'bucket_id' => $bucket->id,
        'pessoa_id' => $client->id,
        'acao_gr' => AcaoGR::Analise,
        'data_solicitacao' => '2026-07-01',
        'data_envio' => '2026-07-02',
        'repeticoes' => 3,
    ]);

    // Associate it with a document/piece to test the document type display
    PecaProcessual::create([
        'task_id' => $taskGR->id,
        'autor_id' => $user->id,
        'tipo_peca_id' => $tipoDocumento->id,
        'data_producao' => '2026-07-02',
        'processo_id' => null,
    ]);

    Livewire::test(ProdutividadeGRTable::class)
        ->assertSee('Cliente GR Importante')
        ->assertSee('Relatório Diário GR')
        ->assertSee('Análise')
        ->assertSee('01/07/2026')
        ->assertSee('02/07/2026')
        ->assertSee('3')
        ->assertDontSee('Tarefa Comum');
});

test('ProdutividadeGRTable shows empty state when no GR tasks exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ProdutividadeGRTable::class)
        ->assertSee('Nenhuma tarefa de GR encontrada');
});
