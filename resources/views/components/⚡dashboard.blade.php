<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Dashboard')] class extends Component
{
    // Aqui no futuro injetaremos os Services para buscar os dados reais do banco
    public string $ganhos = 'R$ 780.000,00';
    public string $perdas = 'R$ 320.000,00';
    public string $custos = 'R$ 180.000,00';
    public string $eficiencia = '78%';
};
?>

<div class="space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Ganhos</p>
                <p class="text-3xl font-bold text-green-600">{{ $ganhos }}</p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full mt-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414 6.707 12.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"></path></svg>
                    +5.2%
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Perdas</p>
                <p class="text-3xl font-bold text-red-600">{{ $perdas }}</p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-medium rounded-full mt-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586 13.293 7.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path></svg>
                    -1.8%
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Custos</p>
                <p class="text-3xl font-bold text-gray-800">{{ $custos }}</p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-700 text-xs font-medium rounded-full mt-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1z"></path></svg>
                    +0.5%
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Eficiência</p>
                <p class="text-3xl font-bold text-gray-800">{{ $eficiencia }}</p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-700 text-xs font-medium rounded-full mt-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 110-12 6 6 0 010 12zM9 9a1 1 0 011-1h2a1 1 0 110 2h-1v2a1 1 0 11-2 0V9z"></path></svg>
                    Estável
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Interações Recentes</h2>
        
        <livewire:interactions-table />
    </div>

</div>