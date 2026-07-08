<div class="space-y-8">
    {{-- Header com Filtros e Voltar --}}
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-200 dark:border-zinc-800">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.produtividade', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim]) }}" wire:navigate
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-slate-500 dark:text-zinc-400 transition-colors">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Produtividade da Equipe</h1>
            </div>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em] ml-11">Visualização focada no desempenho individual dos colaboradores</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Início</span>
                <input type="date" wire:model.live="dataInicio" class="text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-2 py-1 text-slate-700 dark:text-zinc-300 focus:outline-none">
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Fim</span>
                <input type="date" wire:model.live="dataFim" class="text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-2 py-1 text-slate-700 dark:text-zinc-300 focus:outline-none">
            </div>
        </div>
    </header>

    {{-- Tabela do Ranking --}}
    <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Ranking Completo de Documentos / Peças Produzidos</h2>
        </div>
        
        <div class="overflow-x-auto">
            @livewire('dashboard.produtividade-ranking-table', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim])
        </div>
    </div>
</div>
