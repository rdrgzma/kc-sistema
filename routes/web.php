<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota do Dashboard (Página Inicial)
Volt::route('/', 'dashboard')->name('dashboard');

// Rota da Gestão de Pessoas
Volt::route('/pessoas', 'pessoas-table')->name('pessoas.index');
Volt::route('/processos', 'processos-table')->name('processos.index');
Volt::route('/processos/{processo}', 'processo-detalhe')->name('processos.show');
Volt::route('/pessoas/{pessoa}', 'pessoa-detalhe')->name('pessoas.show');

require __DIR__.'/settings.php';
