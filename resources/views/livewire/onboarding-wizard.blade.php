<div class="min-h-screen bg-slate-100 dark:bg-zinc-950 p-8">
    <div class="max-w-5xl mx-auto space-y-8">
        
        {{-- Header Card (Planner Style) --}}
        <div class="flex justify-between items-center bg-white dark:bg-zinc-900 p-8 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800">
            <div>
                <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Onboarding</h1>
                <p class="text-sm text-slate-600 dark:text-zinc-400 mt-2 font-medium italic leading-relaxed">Configuração estratégica para o início de novos atendimentos e processos.</p>
            </div>
            <div class="shrink-0">
                <div class="w-14 h-14 bg-slate-50 dark:bg-zinc-800 rounded-2xl border border-slate-200 dark:border-zinc-700 flex items-center justify-center text-primary-600 dark:text-primary-400 shadow-inner">
                    <x-flux::icon.user-plus class="w-7 h-7" />
                </div>
            </div>
        </div>

        {{-- Alerta de Cliente Existente (Sóbrio) --}}
        @if($pessoaExistenteId)
            <div class="flex items-center gap-5 bg-white dark:bg-zinc-900 p-6 rounded-2xl border-l-[6px] border-emerald-500 border-y border-r border-slate-300 dark:border-zinc-800 shadow-sm transition-all duration-300">
                <div class="shrink-0 w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <x-flux::icon.check-circle class="w-6 h-6" />
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black text-slate-900 dark:text-gray-100 uppercase tracking-widest">Cliente Identificado</p>
                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 font-bold italic mt-0.5">Dados automáticos carregados para este atendimento.</p>
                </div>
                <button wire:click="$set('pessoaExistenteId', null)" class="px-4 py-2 bg-slate-100 dark:bg-zinc-800 text-[10px] font-black text-slate-600 dark:text-zinc-400 rounded-lg hover:bg-slate-200 transition uppercase tracking-[0.2em]">
                    Alterar
                </button>
            </div>
        @endif

        {{-- Form Card (Sober Style) --}}
        <div class="bg-white dark:bg-zinc-900 p-10 lg:p-14 rounded-[3rem] border border-slate-300 dark:border-zinc-800 shadow-sm relative overflow-hidden">
            <form wire:submit="submit">
                {{ $this->form }}
            </form>
        </div>

        {{-- Footer Navigation --}}
        <div class="pt-6 border-t border-slate-300 dark:border-zinc-800 flex justify-center lg:justify-start">
            <a href="{{ route('dashboard') }}" wire:navigate class="group flex items-center gap-4 text-slate-500 hover:text-primary-600 dark:text-zinc-400 dark:hover:text-primary-400 transition-colors">
                <div class="p-3 bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-xl group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20 group-hover:border-primary-400 transition-all shadow-sm">
                    <x-flux::icon.arrow-left class="w-4 h-4" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em]">Retornar ao Dashboard</span>
            </a>
        </div>
    </div>
</div>
