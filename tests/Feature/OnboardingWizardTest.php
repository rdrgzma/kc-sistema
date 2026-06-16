<?php

use App\Livewire\OnboardingWizard;
use App\Models\Area;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('onboarding wizard reuses client matching formatted or raw cpf to prevent unique constraint error', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $escritorio = Escritorio::create(['nome' => 'K&C Advogados']);
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);

    // Create an existing person with a formatted CPF
    $pessoa = Pessoa::create([
        'tipo' => 'PF',
        'nome_razao' => 'Admin Fabrica da Net',
        'cpf_cnpj' => '123.456.789-00',
        'email' => 'admin@admin.com',
        'telefone' => '5192888828',
        'escritorio_id' => $escritorio->id,
    ]);

    // Test with unformatted CPF
    Livewire::test(OnboardingWizard::class)
        ->set('data.tipo', 'PF')
        ->set('data.cpf_cnpj', '12345678900') // Raw value
        ->set('data.nome_razao', 'Admin Fabrica da Net')
        ->set('data.email', 'admin@admin.com')
        ->set('data.telefone', '5192888828')
        ->set('data.numero_processo', '0000001-99.2026.8.21.0001')
        ->set('data.area_id', $area->id)
        ->set('data.fase_id', $fase->id)
        ->set('data.procedimento_id', $procedimento->id)
        ->set('data.tipo_evento', 'J')
        ->set('data.descricao_evento', 'Primeiro andamento')
        ->set('data.data_evento', now()->toDateTimeString())
        ->call('submit')
        ->assertHasNoErrors();

    // Verify no duplicate Pessoa was created
    expect(Pessoa::count())->toBe(1);

    // Verify the new process was linked to the existing Pessoa
    $processo = Processo::where('numero_processo', '0000001-99.2026.8.21.0001')->first();
    expect($processo)->not->toBeNull();
    expect($processo->pessoa_id)->toBe($pessoa->id);
});
