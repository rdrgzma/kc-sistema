<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota do Dashboard (Página Inicial)
Volt::route('/', 'dashboard')->name('dashboard');

// Rota da Gestão de Pessoas
Volt::route('/pessoas', 'pessoas-table')->name('pessoas.index');

require __DIR__.'/settings.php';
