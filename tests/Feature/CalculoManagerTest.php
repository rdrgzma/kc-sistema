<?php

use App\Livewire\CalculoManager;
use App\Models\Indexador;
use App\Models\IndexadorCotacao;
use App\Models\User;
use App\Services\BcbSgsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from calculations page', function () {
    $this->get(route('calculos.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit calculations page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('calculos.index'))->assertOk();
});

test('can run sync_indices table action and populate indexador_cotacoes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create an indexer that needs sync
    $indexador = Indexador::create([
        'nome' => 'IPCA',
        'sigla' => 'IPCA',
        'categoria' => 'Inflação',
        'codigo_sgs' => 433, // SGS Code for IPCA
        'tipo' => 'INFLACAO',
        'fonte' => 'BCB_SGS',
        'is_composto' => false,
    ]);

    // Mock BcbSgsService to return fake historical data
    $this->mock(BcbSgsService::class, function ($mock) {
        $mock->shouldReceive('fetchHistorico')
            ->once()
            ->with(433)
            ->andReturn([
                ['data_referencia' => '2026-01-01', 'valor' => 0.5],
                ['data_referencia' => '2026-02-01', 'valor' => 0.6],
            ]);
    });

    Livewire::test(CalculoManager::class)
        ->callTableAction('sync_indices')
        ->assertHasNoTableActionErrors();

    // Verify indexador_cotacoes was populated correctly
    expect(IndexadorCotacao::count())->toBe(2);

    $cotacao1 = IndexadorCotacao::where('data_referencia', '2026-01-01')->first();
    $cotacao2 = IndexadorCotacao::where('data_referencia', '2026-02-01')->first();

    expect($cotacao1)->not->toBeNull();
    expect($cotacao2)->not->toBeNull();

    // The calculation logic multiplies:
    // accumulated_1 = 1 * (1 + 0.5 / 100) = 1.005
    // accumulated_2 = 1.005 * (1 + 0.6 / 100) = 1.005 * 1.006 = 1.01103
    expect((float) $cotacao1->valor)->toBe(1.005);
    expect(round((float) $cotacao2->valor, 5))->toBe(1.01103);
});
