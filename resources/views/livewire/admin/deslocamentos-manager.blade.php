<div class="space-y-8">
    {{-- Header com Voltar --}}
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-200 dark:border-zinc-800">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.produtividade') }}" wire:navigate
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-slate-500 dark:text-zinc-400 transition-colors">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Deslocamentos da Equipe</h1>
            </div>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em] ml-11">Acompanhamento e registro de viagens, audiências e diligências presenciais</p>
        </div>
    </header>

    {{-- Tabela da Produtividade --}}
    <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Lista de Deslocamentos</h2>
        </div>
        
        <div class="overflow-x-auto">
            {{ $this->table }}
        </div>
    </div>

    <x-filament-actions::modals />
</div>
