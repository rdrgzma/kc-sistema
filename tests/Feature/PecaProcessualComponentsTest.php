<?php

use App\Livewire\Planner\TaskPecaProcessual;
use App\Livewire\Processo\PecasRelationManager;
use App\Models\Area;
use App\Models\Bucket;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\PecaProcessual;
use App\Models\Planner;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\Task;
use App\Models\TipoPeca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('TaskPecaProcessual can render and register a piece processual', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $escritorio = Escritorio::factory()->create();
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'escritorio_id' => $escritorio->id,
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
    ]);

    $planner = Planner::create([
        'name' => 'Planner Test',
        'user_id' => $user->id,
        'plannable_id' => $processo->id,
        'plannable_type' => Processo::class,
    ]);

    $bucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'A Fazer',
    ]);

    $task = Task::create([
        'title' => 'Tarefa Test',
        'bucket_id' => $bucket->id,
        'processo_id' => $processo->id,
    ]);

    $tipoPeca = TipoPeca::create(['nome' => 'Contestação']);

    Livewire::test(TaskPecaProcessual::class, ['task' => $task])
        ->assertSee('Nenhuma peça registrada')
        ->callAction('registrarPeca', data: [
            'tipo_peca_id' => $tipoPeca->id,
            'data_producao' => now()->toDateString(),
            'observacoes' => 'Peça criada via Livewire Action',
        ])
        ->assertHasNoActionErrors();

    expect(PecaProcessual::count())->toBe(1);

    $peca = PecaProcessual::first();
    expect($peca->tipo_peca_id)->toBe($tipoPeca->id);
    expect($peca->task_id)->toBe($task->id);
    expect($peca->processo_id)->toBe($processo->id);
});

test('PecasRelationManager can render and register a piece processual', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $escritorio = Escritorio::factory()->create();
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'escritorio_id' => $escritorio->id,
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
    ]);

    $tipoPeca = TipoPeca::create(['nome' => 'Contestação']);

    Livewire::test(PecasRelationManager::class, ['processo' => $processo])
        ->assertSee('Nenhuma peça processual registrada')
        ->callTableAction('create', data: [
            'tipo_peca_id' => $tipoPeca->id,
            'data_producao' => now()->toDateString(),
            'observacoes' => 'Criado na tabela de Peças',
        ])
        ->assertHasNoTableActionErrors();

    expect(PecaProcessual::count())->toBe(1);

    $peca = PecaProcessual::first();
    expect($peca->tipo_peca_id)->toBe($tipoPeca->id);
    expect($peca->processo_id)->toBe($processo->id);
});
