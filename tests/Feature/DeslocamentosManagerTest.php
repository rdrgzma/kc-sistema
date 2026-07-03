<?php

use App\Enums\ModalidadeAtividade;
use App\Enums\TipoAtividadeDeslocamento;
use App\Livewire\Admin\DeslocamentosManager;
use App\Models\ApontamentoTempo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from displacements manager', function () {
    $response = $this->get(route('dashboard.produtividade-deslocamentos'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the displacements manager', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard.produtividade-deslocamentos'));
    $response->assertOk();
});

test('the table shows only presencial activities', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $presencial = ApontamentoTempo::create([
        'user_id' => $user->id,
        'modalidade' => ModalidadeAtividade::PRESENCIAL,
        'tipo_atividade' => TipoAtividadeDeslocamento::AUDIENCIA,
        'local' => 'Fórum Central',
        'data_atividade' => now(),
        'hora_inicio' => '10:00',
        'hora_fim' => '12:00',
    ]);

    $online = ApontamentoTempo::create([
        'user_id' => $user->id,
        'modalidade' => ModalidadeAtividade::ONLINE,
        'tipo_atividade' => TipoAtividadeDeslocamento::REUNIAO,
        'local' => null,
        'data_atividade' => now(),
        'hora_inicio' => '14:00',
        'hora_fim' => '15:00',
    ]);

    Livewire::test(DeslocamentosManager::class)
        ->assertCanSeeTableRecords([$presencial])
        ->assertCanNotSeeTableRecords([$online]);
});
