<?php

use App\Livewire\Admin\FasesManager;
use App\Livewire\Dashboard;
use App\Livewire\OnboardingWizard;
use App\Livewire\PessoaDetalhe;
use App\Livewire\PessoasTable;
use App\Livewire\PlannerBoard;
use App\Livewire\ProcessoDetalhe;
use App\Livewire\ProcessosTable;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect()->route("login");
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
// Gestão Admin
Route::middleware(['role:Administrador|Sócio'])->group(function () {
    Route::get('/admin/fases', FasesManager::class)->name('admin.fases');
    Route::get('/admin/especialidades', \App\Livewire\Admin\EspecialidadesManager::class)->name('admin.especialidades');
    Route::get('/admin/usuarios', \App\Livewire\Admin\UsersManager::class)->name('admin.users');
    Route::get('/admin/escritorios', \App\Livewire\Admin\EscritoriosManager::class)->name('admin.escritorios');
    Route::get('/admin/equipes', \App\Livewire\Admin\EquipesManager::class)->name('admin.equipes');
    Route::get('/admin/peritos', \App\Livewire\Admin\PeritosManager::class)->name('admin.peritos');
    Route::get('/admin/assistentes', \App\Livewire\Admin\AssistentesTecnicosManager::class)->name('admin.assistentes');
});
});

require __DIR__.'/settings.php';
