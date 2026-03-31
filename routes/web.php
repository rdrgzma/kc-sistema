<?php

use App\Http\Controllers\FinanceiroReportController;
use App\Livewire\Admin\AssistentesTecnicosManager;
use App\Livewire\Admin\EquipesManager;
use App\Livewire\Admin\EscritoriosManager;
use App\Livewire\Admin\EspecialidadesManager;
use App\Livewire\Admin\FasesManager;
use App\Livewire\Admin\PeritosManager;
use App\Livewire\Admin\UsersManager;
use App\Livewire\Dashboard;
use App\Livewire\Financeiro\FinanceiroManager;
use App\Livewire\OnboardingWizard;
use App\Livewire\PessoaDetalhe;
use App\Livewire\PessoasTable;
use App\Livewire\PlannerBoard;
use App\Livewire\ProcessoDetalhe;
use App\Livewire\ProcessosTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Rota do Dashboard (Página Inicial)
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');

    // Rota da Gestão de Pessoas
    Route::livewire('/pessoas', PessoasTable::class)->name('pessoas.index');
    Route::livewire('/processos', ProcessosTable::class)->name('processos.index');
    Route::livewire('/processos/{processo}', ProcessoDetalhe::class)->name('processos.show');
    Route::livewire('/pessoas/{pessoa}', PessoaDetalhe::class)->name('pessoas.show');

    // Rota do Onboarding (Fluxo)
    Route::livewire('/onboarding', OnboardingWizard::class)->name('onboarding');
    Route::livewire('/planners', PlannerBoard::class)->name('planners.index');
    Route::get('/admin/fases', FasesManager::class)->name('admin.fases');

    // Módulo Financeiro (Acesso Restrito)
    Route::middleware(['role:Administrador|Sócio'])->group(function () {
        Route::get('/financeiro', FinanceiroManager::class)->name('financeiro.index');
        Route::get('/financeiro/report', [FinanceiroReportController::class, 'export'])->name('admin.financeiro.report');

        Route::get('/admin/fases', FasesManager::class)->name('admin.fases');
        Route::get('/admin/especialidades', EspecialidadesManager::class)->name('admin.especialidades');
        Route::get('/admin/usuarios', UsersManager::class)->name('admin.users');
        Route::get('/admin/escritorios', EscritoriosManager::class)->name('admin.escritorios');
        Route::get('/admin/equipes', EquipesManager::class)->name('admin.equipes');
        Route::get('/admin/peritos', PeritosManager::class)->name('admin.peritos');
        Route::get('/admin/assistentes', AssistentesTecnicosManager::class)->name('admin.assistentes');
    });
});

require __DIR__.'/settings.php';
