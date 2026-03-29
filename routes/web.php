<?php

use App\Livewire\Dashboard;
use App\Livewire\OnboardingWizard;
use App\Livewire\PessoaDetalhe;
use App\Livewire\PessoasTable;
use App\Livewire\PlannerBoard;
use App\Livewire\ProcessoDetalhe;
use App\Livewire\ProcessosTable;
use Illuminate\Support\Facades\Route;

// Rota do Dashboard (Página Inicial)
Route::livewire('/', Dashboard::class)->name('dashboard');

// Rota da Gestão de Pessoas
Route::livewire('/pessoas', PessoasTable::class)->name('pessoas.index');
Route::livewire('/processos', ProcessosTable::class)->name('processos.index');
Route::livewire('/processos/{processo}', ProcessoDetalhe::class)->name('processos.show');
Route::livewire('/pessoas/{pessoa}', PessoaDetalhe::class)->name('pessoas.show');

// Rota do Onboarding (Fluxo)
Route::livewire('/onboarding', OnboardingWizard::class)->name('onboarding');
Route::livewire('/planners', PlannerBoard::class)->name('planners.index');
require __DIR__.'/settings.php';
