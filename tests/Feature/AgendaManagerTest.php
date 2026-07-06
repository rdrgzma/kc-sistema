<?php

use App\Livewire\AgendaManager;
use App\Models\Agendamento;
use App\Models\Area;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from agenda manager', function () {
    $response = $this->get(route('agenda.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the agenda manager', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('agenda.index'));
    $response->assertOk();
});

test('can create an agenda record and automatically set tenancy', function () {
    $escritorio = Escritorio::create(['nome' => 'K&C Teste']);
    $user = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $this->actingAs($user);

    // Seeding foreign key tables dynamically for Processo
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
        'escritorio_id' => $escritorio->id,
    ]);

    Livewire::test(AgendaManager::class)
        ->callTableAction('create', data: [
            'title' => 'Reunião Inicial',
            'description' => 'Apresentação de resultados',
            'user_id' => $user->id,
            'processo_id' => $processo->id,
            'starts_at' => '2026-07-06 15:00:00',
            'ends_at' => '2026-07-06 16:00:00',
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('agendamentos', [
        'title' => 'Reunião Inicial',
        'user_id' => $user->id,
        'processo_id' => $processo->id,
        'escritorio_id' => $escritorio->id,
        'starts_at' => '2026-07-06 15:00:00',
        'ends_at' => '2026-07-06 16:00:00',
    ]);

    $this->assertDatabaseHas('activity_log', [
        'description' => 'Compromisso agendado',
        'subject_type' => Agendamento::class,
        'causer_id' => $user->id,
    ]);
});

test('the table can filter agendamentos by user_id', function () {
    $escritorio = Escritorio::create(['nome' => 'K&C Teste']);
    $user1 = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $user2 = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $this->actingAs($user1);

    $agenda1 = Agendamento::create([
        'title' => 'Agendamento do Usuario 1',
        'user_id' => $user1->id,
        'escritorio_id' => $escritorio->id,
        'starts_at' => '2026-07-06 10:00:00',
        'ends_at' => '2026-07-06 11:00:00',
    ]);

    $agenda2 = Agendamento::create([
        'title' => 'Agendamento do Usuario 2',
        'user_id' => $user2->id,
        'escritorio_id' => $escritorio->id,
        'starts_at' => '2026-07-06 12:00:00',
        'ends_at' => '2026-07-06 13:00:00',
    ]);

    // Test with default / no filters - should see both
    Livewire::test(AgendaManager::class)
        ->assertCanSeeTableRecords([$agenda1, $agenda2]);

    // Test filtering by user1
    Livewire::test(AgendaManager::class, ['userId' => $user1->id])
        ->assertCanSeeTableRecords([$agenda1])
        ->assertCanNotSeeTableRecords([$agenda2]);
});

test('can edit an existing agendamento', function () {
    $escritorio = Escritorio::create(['nome' => 'K&C Teste']);
    $user = User::factory()->create(['escritorio_id' => $escritorio->id]);
    $this->actingAs($user);

    $agenda = Agendamento::create([
        'title' => 'Compromisso Antigo',
        'user_id' => $user->id,
        'escritorio_id' => $escritorio->id,
        'starts_at' => '2026-07-06 10:00:00',
        'ends_at' => '2026-07-06 11:00:00',
    ]);

    Livewire::test(AgendaManager::class)
        ->callTableAction('edit', $agenda, data: [
            'title' => 'Compromisso Novo Atualizado',
            'user_id' => $user->id,
            'starts_at' => '2026-07-06 10:30:00',
            'ends_at' => '2026-07-06 11:30:00',
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('agendamentos', [
        'id' => $agenda->id,
        'title' => 'Compromisso Novo Atualizado',
        'starts_at' => '2026-07-06 10:30:00',
        'ends_at' => '2026-07-06 11:30:00',
    ]);
});
