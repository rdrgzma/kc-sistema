<?php

use App\Enums\ClassificacaoDecisao;
use App\Enums\ModalidadeAtividade;
use App\Enums\StatusFinanceiroDecisao;
use App\Enums\TipoAtividadeDeslocamento;
use App\Livewire\DashboardProdutividade;
use App\Models\ApontamentoTempo;
use App\Models\Area;
use App\Models\Fase;
use App\Models\PecaProcessual;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\TipoPeca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('enums resolve correctly', function () {
    expect(TipoAtividadeDeslocamento::AUDIENCIA->getLabel())->toBe('Audiência');
    expect(ModalidadeAtividade::ONLINE->getLabel())->toBe('On-line');
    expect(ClassificacaoDecisao::FAVORAVEL->getLabel())->toBe('Favorável');
    expect(StatusFinanceiroDecisao::SUB_JUDICE->getLabel())->toBe('Sub judice');
});

test('can create apontamento tempo and calculate displacement time via property hook', function () {
    $user = User::factory()->create();

    $apontamento = ApontamentoTempo::create([
        'user_id' => $user->id,
        'tipo_atividade' => TipoAtividadeDeslocamento::AUDIENCIA,
        'descricao' => 'Audiência de Conciliação',
        'modalidade' => ModalidadeAtividade::PRESENCIAL,
        'local' => 'Fórum Central',
        'data_atividade' => now()->toDateString(),
        'hora_inicio' => '13:00:00',
        'hora_fim' => '14:30:00',
    ]);

    expect($apontamento->tempo_deslocamento)->toBe(90);
    expect($apontamento->user->id)->toBe($user->id);
});

test('can update sentenca and cast fields correctly', function () {
    $sentenca = Sentenca::create([
        'nome' => 'Sentença favorável de exemplo',
        'classificacao' => ClassificacaoDecisao::FAVORAVEL,
        'tipo_decisao' => 'Definitiva',
        'valor_economia' => 15000.50,
        'valor_perda' => 0.00,
        'status_financeiro' => StatusFinanceiroDecisao::TRANSITO_EM_JULGADO,
    ]);

    expect($sentenca->classificacao)->toBe(ClassificacaoDecisao::FAVORAVEL);
    expect($sentenca->status_financeiro)->toBe(StatusFinanceiroDecisao::TRANSITO_EM_JULGADO);
    expect($sentenca->valor_economia)->toBe('15000.50');
    expect($sentenca->valor_perda)->toBe('0.00');
});

test('can create peca processual and access author and process relationships', function () {
    $user = User::factory()->create();

    // Seed foreign key tables dynamically for Processo factory
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
    ]);

    $peca = PecaProcessual::create([
        'processo_id' => $processo->id,
        'autor_id' => $user->id,
        'tipo_peca_id' => TipoPeca::create(['nome' => 'Contestação'])->id,
        'data_producao' => now()->toDateString(),
        'observacoes' => 'Contestação elaborada no prazo',
    ]);

    expect($peca->autor->id)->toBe($user->id);
    expect($peca->processo->id)->toBe($processo->id);
    expect($peca->tipoPeca->nome)->toBe('Contestação');

    // Verify Processo and User hasMany relations
    expect($processo->pecasProcessuais)->toHaveCount(1);
    expect($user->pecasProcessuais)->toHaveCount(1);
});

test('can access dashboard productivity page and retrieve stats, chart data, and ranking', function () {
    $user = User::factory()->create();

    // Authenticate the user
    $this->actingAs($user);

    $response = $this->get(route('dashboard.produtividade'));
    $response->assertStatus(200);

    // Call livewire component
    $component = Livewire::test(DashboardProdutividade::class);
    $component->assertSee('Dashboard de Produtividade');

    $stats = $component->get('stats');
    expect($stats)->toBeArray();
    expect($component->get('chartData'))->toBeInstanceOf(Collection::class);
});
