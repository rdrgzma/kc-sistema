<div class="max-w-full mx-auto py-8 px-8 lg:px-12 transition-all duration-500">
    <div class="mb-12 flex justify-between items-end border-b border-slate-300 dark:border-zinc-800 pb-10">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-3 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">
                    <li><a href="{{ route('processos.index') }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Processos</a></li>
                    <li><x-flux::icon.chevron-right class="w-3 h-3 text-slate-300" /></li>
                    <li class="text-slate-900 dark:text-gray-200">{{ $processo->numero_processo }}</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-slate-900 dark:text-gray-200 tracking-tight">{{ $processo->pessoa->nome_razao }}</h1>
            <p class="text-[11px] font-black text-slate-500 dark:text-gray-500 mt-4 uppercase tracking-[0.2em]">
                Nº Processual: <span class="font-mono bg-slate-200 dark:bg-zinc-800 px-3 py-1 rounded-lg text-primary-700 dark:text-primary-400 ml-2 border border-slate-300 dark:border-zinc-700">{{ $processo->numero_processo }}</span>
            </p>
        </div>
        
        <span class="inline-flex items-center px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.25em] bg-primary-100 text-primary-700 dark:text-primary-400 dark:bg-primary-900/40 border border-primary-200/50">
            Ativo
        </span>
    </div>

    {{-- Panorama Estratégico Topo --}}
    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-6 bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8">
        <div class="flex items-center gap-6">
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800/30">
                <x-heroicon-o-banknotes class="w-8 h-8 text-emerald-600 dark:text-emerald-400"/>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1 italic">Economia Gerada</p>
                <p class="text-3xl font-black text-emerald-700 dark:text-emerald-400">R$ {{ number_format($processo->economia_gerada, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-2xl border border-rose-100 dark:border-rose-800/30">
                <x-heroicon-o-arrow-trending-down class="w-8 h-8 text-rose-600 dark:text-rose-400"/>
            </div>
            <div>
                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1 italic">Perda Estimada</p>
                <p class="text-3xl font-black text-rose-700 dark:text-rose-400">R$ {{ number_format($processo->perda_estimada, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Content Cluster Full-Width --}}
    <div 
        class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 transition-all duration-300" 
        x-data="{ tab: 'cronologia' }"
        :class="['financeiro', 'documentos'].includes(tab) ? 'p-4 lg:p-6' : 'p-6 lg:p-10'"
    >
        {{-- Cluster Navigation --}}
        <div class="flex flex-wrap p-1.5 bg-slate-100 dark:bg-zinc-800 rounded-3xl mb-10 w-fit gap-1">
            <button 
                @click="tab = 'cronologia'" 
                :class="tab === 'cronologia' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-clock class="w-5 h-5"/>
                Cronologia
            </button>
            <button 
                @click="tab = 'financeiro'" 
                :class="tab === 'financeiro' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-currency-dollar class="w-5 h-5"/>
                Financeiro
            </button>
            <button 
                @click="tab = 'documentos'" 
                :class="tab === 'documentos' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-document-duplicate class="w-5 h-5"/>
                Documentos
            </button>
            <button 
                @click="tab = 'atendimentos'" 
                :class="tab === 'atendimentos' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5"/>
                Atendimentos
            </button>
        </div>

        {{-- Tab Panels Full-Width --}}
        <div x-show="tab === 'cronologia'" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <livewire:timeline-feed :model="$processo" />
        </div>

        <div x-show="tab === 'financeiro'" x-cloak x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:financeiro.financeiro-manager :model="$processo" />
            </div>
        </div>

        <div x-show="tab === 'documentos'" x-cloak x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:document-manager :model="$processo" />
            </div>
        </div>

        <div x-show="tab === 'atendimentos'" x-cloak x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:interacao-manager :model="$processo" />
            </div>
        </div>
    </div>
</div>