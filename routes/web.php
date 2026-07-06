<?php

use App\Http\Controllers\AnalyticsReportController;
use App\Http\Controllers\FinanceiroReportController;
use App\Http\Controllers\ProdutividadeReportController;
use App\Livewire\Admin\AnalyticsManager;
use App\Livewire\Admin\ApontamentosManager;
use App\Livewire\Admin\AssistentesTecnicosManager;
use App\Livewire\Admin\DeslocamentosManager;
use App\Livewire\Admin\EquipesManager;
use App\Livewire\Admin\EscritoriosManager;
use App\Livewire\Admin\EspecialidadesManager;
use App\Livewire\Admin\FasesManager;
use App\Livewire\Admin\PeritosManager;
use App\Livewire\Admin\ProdutividadeUsuarioManager;
use App\Livewire\Admin\UsersManager;
use App\Livewire\AgendaManager;
use App\Livewire\CalculoManager;
use App\Livewire\Dashboard;
use App\Livewire\DashboardProdutividade;
use App\Livewire\DashboardProdutividadeEquipe;
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
    Route::get('/dashboard/produtividade', DashboardProdutividade::class)->name('dashboard.produtividade');
    Route::get('/dashboard/produtividade/equipe', DashboardProdutividadeEquipe::class)->name('dashboard.produtividade-equipe');
    Route::get('/dashboard/produtividade/usuarios', ProdutividadeUsuarioManager::class)->name('dashboard.produtividade-usuarios');
    Route::get('/dashboard/produtividade/deslocamentos', DeslocamentosManager::class)->name('dashboard.produtividade-deslocamentos');
    Route::get('/dashboard/produtividade/exportar-decisoes', [ProdutividadeReportController::class, 'exportDecisoes'])->name('produtividade.exportar-decisoes');
    Route::get('/dashboard/produtividade/exportar-apontamentos', [ProdutividadeReportController::class, 'exportApontamentos'])->name('produtividade.exportar-apontamentos');

    // Rota da Gestão de Pessoas
    Route::livewire('/pessoas', PessoasTable::class)->name('pessoas.index');
    Route::livewire('/processos', ProcessosTable::class)->name('processos.index');
    Route::livewire('/processos/{processo}', ProcessoDetalhe::class)->name('processos.show');
    Route::livewire('/pessoas/{pessoa}', PessoaDetalhe::class)->name('pessoas.show');
    Route::get('/apontamentos', ApontamentosManager::class)->name('apontamentos.index');
    Route::get('/calculos', CalculoManager::class)->name('calculos.index');
    Route::get('/operacional/agenda', AgendaManager::class)->name('agenda.index');

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
        Route::get('/admin/analytics', AnalyticsManager::class)->name('admin.analytics');
        Route::get('/admin/analytics/export/csv', [AnalyticsReportController::class, 'exportCsv'])->name('admin.analytics.export.csv');
        Route::get('/admin/analytics/export/pdf', [AnalyticsReportController::class, 'exportPdf'])->name('admin.analytics.export.pdf');
    });
});

require __DIR__.'/settings.php';
