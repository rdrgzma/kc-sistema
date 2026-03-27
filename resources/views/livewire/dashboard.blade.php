<?php

use Livewire\Volt\Component;
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
        
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Ganhos</p>
            <p class="text-3xl font-black text-emerald-500 dark:text-emerald-400 mt-2 tracking-tight">{{ $ganhos }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Perdas</p>
            <p class="text-3xl font-black text-rose-500 dark:text-rose-400 mt-2 tracking-tight">{{ $perdas }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Custos</p>
            <p class="text-3xl font-black text-amber-500 dark:text-amber-400 mt-2 tracking-tight">{{ $custos }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Eficiência</p>
            <p class="text-3xl font-black text-sky-500 dark:text-sky-400 mt-2 tracking-tight">{{ $eficiencia }}</p>
        </div>

    </div>

    <!-- Row 2: Colored summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-600 dark:bg-blue-700 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02]">
            <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Delegadas</p>
            <p class="text-3xl font-black text-white mt-1">180</p>
        </div>
        <div class="bg-emerald-500 dark:bg-emerald-600 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02]">
            <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Concluídas</p>
            <p class="text-3xl font-black text-white mt-1">140</p>
        </div>
        <div class="bg-indigo-600 dark:bg-indigo-700 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02]">
            <p class="text-xs font-bold text-white/70 uppercase tracking-wider">No Prazo</p>
            <p class="text-3xl font-black text-white mt-1">110</p>
        </div>
        <div class="bg-rose-500 dark:bg-rose-600 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02]">
            <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Atrasadas</p>
            <p class="text-3xl font-black text-white mt-1">30</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm">
            <h2 class="text-xl font-black text-gray-900 dark:text-zinc-50 mb-6">Financeiro</h2>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-zinc-800/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-zinc-700">
                <p class="text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest text-xs">Gráfico Financeiro</p>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm">
            <h2 class="text-xl font-black text-gray-900 dark:text-zinc-50 mb-6">Interações</h2>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-zinc-800/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-zinc-700">
                 <p class="text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest text-xs">Gráfico Interações</p>
            </div>
        </div>
    </div>

</div>