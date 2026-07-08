<?php

use App\Models\Area;
use App\Models\Bucket;
use App\Models\Equipe;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\Planner;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('isolates task queries based on user team and assignments', function () {
    // Seed lookup tables needed by Processo factory
    $escritorio = Escritorio::factory()->create();
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    // Setup das equipes
    $equipeA = Equipe::create(['nome' => 'Equipa A']);
    $equipeB = Equipe::create(['nome' => 'Equipa B']);

    // Setup dos usuários
    $user1 = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $user1->equipes()->attach($equipeA);

    $user2 = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $user2->equipes()->attach($equipeA);

    $user3 = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $user3->equipes()->attach($equipeB);

    // Setup de Processos vinculados às equipes
    $processoEquipeA = Processo::factory()->create([
        'equipe_id' => $equipeA->id,
        'responsavel_id' => $user1->id,
    ]);
    $processoEquipeB = Processo::factory()->create([
        'equipe_id' => $equipeB->id,
        'responsavel_id' => $user3->id,
    ]);

    // Planner + Bucket da Equipa A (dono: user1)
    $plannerA = Planner::create([
        'name' => 'Planner Equipa A',
        'user_id' => $user1->id,
        'plannable_id' => $processoEquipeA->id,
        'plannable_type' => Processo::class,
    ]);
    $bucketA = Bucket::create([
        'planner_id' => $plannerA->id,
        'name' => 'A Fazer',
    ]);

    // Planner + Bucket da Equipa B (dono: user3)
    $plannerB = Planner::create([
        'name' => 'Planner Equipa B',
        'user_id' => $user3->id,
        'plannable_id' => $processoEquipeB->id,
        'plannable_type' => Processo::class,
    ]);
    $bucketB = Bucket::create([
        'planner_id' => $plannerB->id,
        'name' => 'A Fazer',
    ]);

    // Tarefa 1: Equipa A, assigned_to user1
    $task1 = Task::create([
        'title' => 'Tarefa da Equipa A - User 1',
        'processo_id' => $processoEquipeA->id,
        'assigned_to' => $user1->id,
        'bucket_id' => $bucketA->id,
    ]);

    // Tarefa 2: Equipa A, assigned_to user2
    $task2 = Task::create([
        'title' => 'Tarefa da Equipa A - User 2',
        'processo_id' => $processoEquipeA->id,
        'assigned_to' => $user2->id,
        'bucket_id' => $bucketA->id,
    ]);

    // Tarefa 3: Equipa B, assigned_to user3
    $task3 = Task::create([
        'title' => 'Tarefa da Equipa B - User 3',
        'processo_id' => $processoEquipeB->id,
        'assigned_to' => $user3->id,
        'bucket_id' => $bucketB->id,
    ]);

    // --- TESTES PARA USER 1 (Equipa A) ---
    $this->actingAs($user1);

    // User 1 deve ver task 1 (dele próprio) e task 2 (do colega de Equipa A no processo A)
    $tasksUser1 = Task::query()->estratificado()->get();

    expect($tasksUser1->pluck('id')->toArray())
        ->toContain($task1->id)
        ->toContain($task2->id)
        ->not->toContain($task3->id);

    // --- TESTES PARA USER 2 (Equipa A) ---
    $this->actingAs($user2);

    // User 2 deve ver task 2 e task 1
    $tasksUser2 = Task::query()->estratificado()->get();

    expect($tasksUser2->pluck('id')->toArray())
        ->toContain($task1->id)
        ->toContain($task2->id)
        ->not->toContain($task3->id);

    // --- TESTES PARA USER 3 (Equipa B) ---
    $this->actingAs($user3);

    // User 3 só vê a task 3
    $tasksUser3 = Task::query()->estratificado()->get();

    expect($tasksUser3->pluck('id')->toArray())
        ->toContain($task3->id)
        ->not->toContain($task1->id)
        ->not->toContain($task2->id);
});

it('returns correct permissions array in headless api endpoint', function () {
    $user = User::factory()->create();

    // Criar permissões de teste
    $perm1 = Permission::create(['name' => 'view_any_task']);
    $perm2 = Permission::create(['name' => 'create_processo']);

    // Atribuir diretamente ao user
    $user->givePermissionTo($perm1);
    $user->givePermissionTo($perm2);

    $this->actingAs($user);

    // Como o endpoint criado foi /api/user e não /api/me (como padrão Laravel)
    $response = $this->getJson('/api/user');

    $response->assertStatus(200);

    $data = $response->json();

    expect($data)->toHaveKey('permissions');
    expect($data['permissions'])->toBeArray();
    expect($data['permissions'])
        ->toContain('view_any_task')
        ->toContain('create_processo');
});
