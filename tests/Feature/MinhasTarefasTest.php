<?php

use App\Livewire\MinhasTarefas;
use App\Models\Bucket;
use App\Models\Pessoa;
use App\Models\Planner;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('MinhasTarefas component can render and shows tasks assigned to the user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Test Planner',
        'user_id' => $user->id,
        'plannable_id' => $user->id,
        'plannable_type' => User::class,
    ]);

    $bucket = Bucket::create([
        'planner_id' => $planner->id,
        'name' => 'A Fazer',
    ]);

    $client = Pessoa::create([
        'nome_razao' => 'Cliente ABC',
        'tipo' => 'Fisica',
    ]);

    // Task assigned to current user
    $myTask = Task::create([
        'title' => 'Minha Super Tarefa',
        'bucket_id' => $bucket->id,
        'assigned_to' => $user->id,
        'pessoa_id' => $client->id,
    ]);

    // Task assigned to someone else
    $otherTask = Task::create([
        'title' => 'Tarefa de Outro',
        'bucket_id' => $bucket->id,
        'assigned_to' => $otherUser->id,
    ]);

    Livewire::test(MinhasTarefas::class)
        ->assertSee('Minha Super Tarefa')
        ->assertSee('Cliente ABC')
        ->assertDontSee('Tarefa de Outro');
});

test('MinhasTarefas hides completed tasks by default and allows showing them via filter', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Test Planner',
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

    // Open task
    $openTask = Task::create([
        'title' => 'Tarefa Aberta',
        'bucket_id' => $openBucket->id,
        'assigned_to' => $user->id,
    ]);

    // Completed task
    $completedTask = Task::create([
        'title' => 'Tarefa Pronta',
        'bucket_id' => $completedBucket->id,
        'assigned_to' => $user->id,
    ]);

    // By default, hides completed
    Livewire::test(MinhasTarefas::class)
        ->assertSee('Tarefa Aberta')
        ->assertDontSee('Tarefa Pronta')
        // Now toggle filter
        ->set('tableFilters.ocultar_concluidas.isActive', false)
        ->assertSee('Tarefa Aberta')
        ->assertSee('Tarefa Pronta');
});

test('MinhasTarefas allows completing a task from actions', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Test Planner',
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
        'title' => 'Tarefa a ser Concluida',
        'bucket_id' => $openBucket->id,
        'assigned_to' => $user->id,
    ]);

    Livewire::test(MinhasTarefas::class)
        ->callTableAction('concluir', $task);

    expect($task->refresh()->bucket_id)->toBe($completedBucket->id);
});

test('MinhasTarefas allows reopening a task from actions', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $planner = Planner::create([
        'name' => 'Test Planner',
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
        'title' => 'Tarefa a ser Reaberta',
        'bucket_id' => $completedBucket->id,
        'assigned_to' => $user->id,
    ]);

    Livewire::test(MinhasTarefas::class)
        ->set('tableFilters.ocultar_concluidas.isActive', false)
        ->callTableAction('reabrir', $task);

    expect($task->refresh()->bucket_id)->toBe($openBucket->id);
});
