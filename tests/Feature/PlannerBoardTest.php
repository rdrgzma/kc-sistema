<?php

use App\Livewire\PlannerBoard;
use App\Models\Bucket;
use App\Models\Planner;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('PlannerBoard can render and select a planner', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Meu Quadro Kanban',
        'user_id' => $user->id,
        'plannable_id' => $user->id,
        'plannable_type' => User::class,
    ]);

    Livewire::test(PlannerBoard::class)
        ->assertSee('Meu Quadro Kanban')
        ->call('selectPlanner', $planner->id)
        ->assertSet('selectedPlannerId', $planner->id);
});

test('PlannerBoard allows completing a task using the inline action', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Meu Quadro Kanban',
        'user_id' => $user->id,
        'plannable_id' => $user->id,
        'plannable_type' => User::class,
    ]);

    $openBucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'A Fazer',
    ]);

    $completedBucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'Concluído',
    ]);

    $task = Task::create([
        'title' => 'Tarefa Kanban a Concluir',
        'bucket_id' => $openBucket->id,
    ]);

    Livewire::test(PlannerBoard::class)
        ->call('selectPlanner', $planner->id)
        ->assertSee('Tarefa Kanban a Concluir')
        ->call('concluirTarefa', $task->id);

    expect($task->refresh()->bucket_id)->toBe($completedBucket->id);

    // Assert timeline Event is registered for the task
    expect($task->timelineEvents()->where('descricao', 'like', '%Tarefa movida para a coluna Concluído.%')->exists())->toBeTrue();
});

test('PlannerBoard allows reopening a task and registers timeline activities', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Meu Quadro Kanban',
        'user_id' => $user->id,
        'plannable_id' => $user->id,
        'plannable_type' => User::class,
    ]);

    $openBucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'A Fazer',
    ]);

    $completedBucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'Concluído',
    ]);

    $task = Task::create([
        'title' => 'Tarefa Kanban a Reabrir',
        'bucket_id' => $completedBucket->id,
    ]);

    Livewire::test(PlannerBoard::class)
        ->call('selectPlanner', $planner->id)
        ->assertSee('Tarefa Kanban a Reabrir')
        ->call('reabrirTarefa', $task->id);

    expect($task->refresh()->bucket_id)->toBe($openBucket->id);
    expect($task->timelineEvents()->where('descricao', 'like', '%Tarefa movida para a coluna A Fazer.%')->exists())->toBeTrue();
});
