<?php

use Illuminate\Support\Facades\Route;

// Rota do Dashboard (Página Inicial)
Route::livewire('/', \App\Livewire\Dashboard::class)->name('dashboard');

// Rota da Gestão de Pessoas
Route::livewire('/pessoas', \App\Livewire\PessoasTable::class)->name('pessoas.index');
Route::livewire('/processos', \App\Livewire\ProcessosTable::class)->name('processos.index');
Route::livewire('/processos/{processo}', \App\Livewire\ProcessoDetalhe::class)->name('processos.show');
Route::livewire('/pessoas/{pessoa}', \App\Livewire\PessoaDetalhe::class)->name('pessoas.show');

// Rota do Onboarding (Fluxo)
Route::livewire('/onboarding', \App\Livewire\OnboardingWizard::class)->name('onboarding');

require __DIR__.'/settings.php';
