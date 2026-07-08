<?php

use App\Enums\ClassificacaoDecisao;
use App\Enums\StatusFinanceiroDecisao;
use App\Livewire\Admin\AreasManager;
use App\Livewire\Admin\ProcedimentosManager;
use App\Livewire\Admin\SeguradorasManager;
use App\Livewire\Admin\SentencasManager;
use App\Livewire\Admin\TipoPecasManager;
use App\Models\Area;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\PecaProcessual;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Seguradora;
use App\Models\Sentenca;
use App\Models\TipoPeca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Sócio', 'guard_name' => 'web']);
});

test('guests are redirected to the login page from Cadastros Base manager routes', function () {
    $this->get(route('admin.areas'))->assertRedirect(route('login'));
    $this->get(route('admin.tipo-pecas'))->assertRedirect(route('login'));
    $this->get(route('admin.procedimentos'))->assertRedirect(route('login'));
    $this->get(route('admin.seguradoras'))->assertRedirect(route('login'));
    $this->get(route('admin.sentencas'))->assertRedirect(route('login'));
});

test('non-admin/non-socio users are forbidden from accessing Cadastros Base manager routes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('admin.areas'))->assertStatus(403);
    $this->get(route('admin.tipo-pecas'))->assertStatus(403);
    $this->get(route('admin.procedimentos'))->assertStatus(403);
    $this->get(route('admin.seguradoras'))->assertStatus(403);
    $this->get(route('admin.sentencas'))->assertStatus(403);
});

test('admin users can access and manage areas via AreasManager component', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrador');
    $this->actingAs($admin);

    $this->get(route('admin.areas'))->assertOk();

    Livewire::test(AreasManager::class)
        ->assertSee('Sem áreas cadastradas')
        ->callTableAction('create', data: [
            'nome' => 'Direito Civil',
        ])
        ->assertHasNoTableActionErrors();

    expect(Area::count())->toBe(1);
    expect(Area::first()->nome)->toBe('Direito Civil');

    $area = Area::first();

    Livewire::test(AreasManager::class)
        ->callTableAction('edit', $area, data: [
            'nome' => 'Direito Civil Alterado',
        ])
        ->assertHasNoTableActionErrors();

    expect($area->refresh()->nome)->toBe('Direito Civil Alterado');

    // Test deletion when Area has no processes
    Livewire::test(AreasManager::class)
        ->callTableAction('delete', $area)
        ->assertHasNoTableActionErrors();

    expect(Area::count())->toBe(0);

    // Test deletion prevented when Area has processes
    $areaWithProcess = Area::create(['nome' => 'Direito Processual']);
    $escritorio = Escritorio::factory()->create();
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'escritorio_id' => $escritorio->id,
        'area_id' => $areaWithProcess->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentenca->id,
    ]);

    Livewire::test(AreasManager::class)
        ->callTableAction('delete', $areaWithProcess);

    expect(Area::count())->toBe(1);
});

test('admin users can access and manage tipo-pecas via TipoPecasManager component', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrador');
    $this->actingAs($admin);

    $this->get(route('admin.tipo-pecas'))->assertOk();

    Livewire::test(TipoPecasManager::class)
        ->assertSee('Sem tipos de documento / peça cadastrados')
        ->callTableAction('create', data: [
            'nome' => 'Petição Inicial',
            'descricao' => 'Peça de introdução da ação',
        ])
        ->assertHasNoTableActionErrors();

    expect(TipoPeca::count())->toBe(1);
    expect(TipoPeca::first()->nome)->toBe('Petição Inicial');

    $tipoPeca = TipoPeca::first();

    Livewire::test(TipoPecasManager::class)
        ->callTableAction('edit', $tipoPeca, data: [
            'nome' => 'Petição Inicial Alterada',
            'descricao' => 'Descrição nova',
        ])
        ->assertHasNoTableActionErrors();

    expect($tipoPeca->refresh()->nome)->toBe('Petição Inicial Alterada');

    // Test deletion when TipoPeca has no processual pieces
    Livewire::test(TipoPecasManager::class)
        ->callTableAction('delete', $tipoPeca)
        ->assertHasNoTableActionErrors();

    expect(TipoPeca::count())->toBe(0);

    // Test deletion prevented when TipoPeca has processual pieces
    $tipoPecaWithPeca = TipoPeca::create(['nome' => 'Quesitos']);
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

    $peca = PecaProcessual::create([
        'processo_id' => $processo->id,
        'autor_id' => $admin->id,
        'tipo_peca_id' => $tipoPecaWithPeca->id,
        'data_producao' => now()->toDateString(),
    ]);

    Livewire::test(TipoPecasManager::class)
        ->callTableAction('delete', $tipoPecaWithPeca);

    expect(TipoPeca::count())->toBe(1);
});

test('admin users can access and manage procedimentos via ProcedimentosManager component', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrador');
    $this->actingAs($admin);

    $this->get(route('admin.procedimentos'))->assertOk();

    Livewire::test(ProcedimentosManager::class)
        ->assertSee('Sem procedimentos cadastrados')
        ->callTableAction('create', data: [
            'nome' => 'Procedimento A',
        ])
        ->assertHasNoTableActionErrors();

    expect(Procedimento::count())->toBe(1);
    expect(Procedimento::first()->nome)->toBe('Procedimento A');

    $procedimento = Procedimento::first();

    Livewire::test(ProcedimentosManager::class)
        ->callTableAction('edit', $procedimento, data: [
            'nome' => 'Procedimento B',
        ])
        ->assertHasNoTableActionErrors();

    expect($procedimento->refresh()->nome)->toBe('Procedimento B');

    // Test deletion when not in use
    Livewire::test(ProcedimentosManager::class)
        ->callTableAction('delete', $procedimento)
        ->assertHasNoTableActionErrors();

    expect(Procedimento::count())->toBe(0);

    // Test deletion prevented when in use by process
    $procInUse = Procedimento::create(['nome' => 'Procedimento C']);
    $escritorio = Escritorio::factory()->create();
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $sentenca = Sentenca::create(['nome' => 'Procedente']);

    $processo = Processo::factory()->create([
        'escritorio_id' => $escritorio->id,
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procInUse->id,
        'sentenca_id' => $sentenca->id,
    ]);

    Livewire::test(ProcedimentosManager::class)
        ->callTableAction('delete', $procInUse);

    expect(Procedimento::count())->toBe(1);
});

test('admin users can access and manage seguradoras via SeguradorasManager component', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrador');
    $this->actingAs($admin);

    $this->get(route('admin.seguradoras'))->assertOk();

    Livewire::test(SeguradorasManager::class)
        ->assertSee('Sem seguradoras cadastradas')
        ->callTableAction('create', data: [
            'nome' => 'Seguradora A',
        ])
        ->assertHasNoTableActionErrors();

    expect(Seguradora::count())->toBe(1);
    expect(Seguradora::first()->nome)->toBe('Seguradora A');

    $seguradora = Seguradora::first();

    Livewire::test(SeguradorasManager::class)
        ->callTableAction('edit', $seguradora, data: [
            'nome' => 'Seguradora B',
        ])
        ->assertHasNoTableActionErrors();

    expect($seguradora->refresh()->nome)->toBe('Seguradora B');

    // Test deletion when not in use
    Livewire::test(SeguradorasManager::class)
        ->callTableAction('delete', $seguradora)
        ->assertHasNoTableActionErrors();

    expect(Seguradora::count())->toBe(0);

    // Test deletion prevented when in use by process
    $segInUse = Seguradora::create(['nome' => 'Seguradora C']);
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
        'seguradora_id' => $segInUse->id,
        'sentenca_id' => $sentenca->id,
    ]);

    Livewire::test(SeguradorasManager::class)
        ->callTableAction('delete', $segInUse);

    expect(Seguradora::count())->toBe(1);
});

test('admin users can access and manage sentencas via SentencasManager component', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrador');
    $this->actingAs($admin);

    $this->get(route('admin.sentencas'))->assertOk();

    Livewire::test(SentencasManager::class)
        ->assertSee('Sem sentenças cadastradas')
        ->callTableAction('create', data: [
            'nome' => 'Sentença A',
            'classificacao' => ClassificacaoDecisao::FAVORAVEL->value,
            'tipo_decisao' => 'Mérito',
            'valor_economia' => 1000.00,
            'valor_perda' => 0.00,
            'status_financeiro' => StatusFinanceiroDecisao::TRANSITO_EM_JULGADO->value,
        ])
        ->assertHasNoTableActionErrors();

    expect(Sentenca::count())->toBe(1);
    expect(Sentenca::first()->nome)->toBe('Sentença A');

    $sentenca = Sentenca::first();

    Livewire::test(SentencasManager::class)
        ->callTableAction('edit', $sentenca, data: [
            'nome' => 'Sentença B',
            'classificacao' => ClassificacaoDecisao::DESFAVORAVEL->value,
            'tipo_decisao' => 'Mérito Alterado',
            'valor_economia' => 0.00,
            'valor_perda' => 500.00,
            'status_financeiro' => StatusFinanceiroDecisao::SUB_JUDICE->value,
        ])
        ->assertHasNoTableActionErrors();

    expect($sentenca->refresh()->nome)->toBe('Sentença B');

    // Test deletion when not in use
    Livewire::test(SentencasManager::class)
        ->callTableAction('delete', $sentenca)
        ->assertHasNoTableActionErrors();

    expect(Sentenca::count())->toBe(0);

    // Test deletion prevented when in use by process
    $sentInUse = Sentenca::create([
        'nome' => 'Sentença C',
        'classificacao' => ClassificacaoDecisao::PARCIAL->value,
        'tipo_decisao' => 'Interlocutória',
        'status_financeiro' => StatusFinanceiroDecisao::SUB_JUDICE->value,
    ]);
    $escritorio = Escritorio::factory()->create();
    $area = Area::create(['nome' => 'Cível']);
    $fase = Fase::create(['nome' => 'Conhecimento']);
    $procedimento = Procedimento::create(['nome' => 'Comum']);

    $processo = Processo::factory()->create([
        'escritorio_id' => $escritorio->id,
        'area_id' => $area->id,
        'fase_id' => $fase->id,
        'procedimento_id' => $procedimento->id,
        'sentenca_id' => $sentInUse->id,
    ]);

    Livewire::test(SentencasManager::class)
        ->callTableAction('delete', $sentInUse);

    expect(Sentenca::count())->toBe(1);
});
