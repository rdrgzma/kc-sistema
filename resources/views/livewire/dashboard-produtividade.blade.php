<div class="space-y-8">
    {{-- Header com Filtros --}}
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-200 dark:border-zinc-800">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Dashboard de Produtividade</h1>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em]">K&C Analytics • Desempenho da Equipe e Deslocamentos</p>
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
            <div class="flex items-end gap-2 h-full">
                <a href="{{ route('produtividade.exportar-decisoes') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Decisões
                </a>
                <a href="{{ route('produtividade.exportar-apontamentos') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Deslocamento
                </a>
            </div>
        </div>
    </header>

    {{-- Cards de Estatísticas --}}
    @php
        $stats = $this->stats;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        {{-- Card 1: Decisões --}}
        <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <p class="text-[9px] md:text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Decisões</p>
                <x-heroicon-o-scale class="w-4 h-4 md:w-5 md:h-5 text-primary-500 shrink-0" />
            </div>
            <div class="flex gap-2 md:gap-4 mt-2">
                <div>
                    <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['favoraveis_mes'] }}</span>
                    <p class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-wider mt-1">Fav.</p>
                </div>
                <div class="border-l border-slate-200 dark:border-zinc-800 pl-2 md:pl-4">
                    <span class="text-xl md:text-2xl font-black text-rose-600 dark:text-rose-400">{{ $stats['desfavoraveis_mes'] }}</span>
                    <p class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-wider mt-1">Desf.</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Economia Gerada --}}
        <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <p class="text-[9px] md:text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Economia Gerada</p>
                <x-heroicon-o-banknotes class="w-4 h-4 md:w-5 md:h-5 text-emerald-500 shrink-0" />
            </div>
            <p class="text-lg sm:text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2 tracking-tight truncate" title="R$ {{ number_format($stats['total_economia'], 2, ',', '.') }}">R$ {{ number_format($stats['total_economia'], 0, ',', '.') }}</p>
        </div>

        {{-- Card 3: Controle de Tarefas --}}
        <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <p class="text-[9px] md:text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Tarefas Concluídas</p>
                <x-heroicon-o-check-circle class="w-4 h-4 md:w-5 md:h-5 text-emerald-500 shrink-0" />
            </div>
            <div class="flex gap-2 md:gap-4 mt-2">
                <div>
                    <span class="text-xl md:text-2xl font-black text-slate-800 dark:text-zinc-100">{{ $stats['tarefas_concluidas'] }}<span class="text-xs text-slate-400 font-normal">/{{ $stats['total_tarefas'] }}</span></span>
                    <p class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-wider mt-1">Concl.</p>
                </div>
                <div class="border-l border-slate-200 dark:border-zinc-800 pl-2 md:pl-4">
                    <span class="text-xl md:text-2xl font-black {{ $stats['tarefas_repeticoes'] > 0 ? 'text-amber-500' : 'text-slate-400' }}">{{ $stats['tarefas_repeticoes'] }}</span>
                    <p class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-wider mt-1">Repetições</p>
                </div>
            </div>
        </div>

        {{-- Card 4: Deslocamento --}}
        <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <p class="text-[9px] md:text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Deslocamento</p>
                <x-heroicon-o-truck class="w-4 h-4 md:w-5 md:h-5 text-blue-500 shrink-0" />
            </div>
            <p class="text-lg sm:text-2xl md:text-3xl font-black text-blue-600 dark:text-blue-400 mt-2 tracking-tight">{{ $stats['total_horas_deslocamento'] }} <span class="text-xs sm:text-sm md:text-lg font-normal">h</span></p>
        </div>
    </div>

    {{-- Rankings de Produtividade --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Ranking de Peças --}}
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 gap-2">
                    <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Ranking de Documentos / Peças</h2>
                    <a href="{{ route('dashboard.produtividade-equipe', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim]) }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800/40 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-300 text-xs font-bold rounded-lg transition-colors border border-slate-200 dark:border-zinc-800 shrink-0">
                        <x-heroicon-o-presentation-chart-line class="w-4 h-4 text-primary-500" />
                        Ver Completo
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    @livewire('dashboard.produtividade-ranking-table', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim])
                </div>
            </div>
        </div>

        {{-- Ranking de Tarefas --}}
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 gap-2">
                    <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Ranking de Tarefas</h2>
                    <a href="{{ route('dashboard.produtividade-usuarios') }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800/40 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-300 text-xs font-bold rounded-lg transition-colors border border-slate-200 dark:border-zinc-800 shrink-0">
                        <x-heroicon-o-presentation-chart-line class="w-4 h-4 text-emerald-500" />
                        Ver Completo
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    @livewire('dashboard.produtividade-tarefas-ranking-table', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim])
                </div>
            </div>
        </div>

        {{-- Ranking de Deslocamentos --}}
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 gap-2">
                    <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Ranking de Deslocamentos</h2>
                    <a href="{{ route('dashboard.produtividade-deslocamentos') }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800/40 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-300 text-xs font-bold rounded-lg transition-colors border border-slate-200 dark:border-zinc-800 shrink-0">
                        <x-heroicon-o-presentation-chart-line class="w-4 h-4 text-blue-500" />
                        Ver Completo
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    @livewire('dashboard.produtividade-deslocamentos-ranking-table', ['dataInicio' => $dataInicio, 'dataFim' => $dataFim])
                </div>
            </div>
        </div>
    </div>

</div>
