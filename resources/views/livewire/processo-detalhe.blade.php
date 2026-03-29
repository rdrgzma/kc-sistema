<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-10">
                <livewire:timeline-feed :model="$processo" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8" >
                <h4 class="text-[10px] font-black text-slate-400 dark:text-gray-200 uppercase tracking-[0.25em] mb-6 border-b border-slate-100 dark:border-zinc-800 pb-3">Panorama Estratégico</h4>
                <div class="space-y-6">
                    <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-5 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Economia Gerada</p>
                        <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400">R$ {{ number_format($processo->economia_gerada, 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-rose-50/50 dark:bg-rose-900/10 p-5 rounded-2xl border border-rose-100">
                        <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Perda Estimada</p>
                        <p class="text-2xl font-black text-rose-700 dark:text-rose-400">R$ {{ number_format($processo->perda_estimada, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 dark:border-zinc-800 pb-3">
                    <x-heroicon-o-folder-open class="w-6 h-6 text-primary-600 dark:text-primary-400"/>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.25em]">Documentação</h4>
                </div>
                <div class="rounded-xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:document-manager :model="$processo" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 dark:border-zinc-800 pb-3">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400"/>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.25em]">Financeiro</h4>
                </div>
                <div class="rounded-xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:financeiro-manager :model="$processo" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8 text-sm">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 dark:border-zinc-800 pb-3">
                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-indigo-600 dark:text-indigo-400"/>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.25em]">Atendimentos</h4>
                </div>
                <div class="rounded-xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:interacao-manager :model="$processo" />
                </div>
            </div>
        </div>
    </div>
</div>