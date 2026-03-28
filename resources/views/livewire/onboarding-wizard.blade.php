<div class="max-w-5xl mx-auto py-12 px-4">
    <div class="mb-10 text-center lg:text-left">
        <h1 class="text-4xl font-black text-gray-900 dark:text-zinc-50 tracking-tighter mb-2">Fluxo de Onboarding</h1>
        <p class="text-lg text-gray-500 dark:text-zinc-400 font-medium tracking-tight">
            Configure um novo atendimento rapidamente.
        </p>
    </div>

    {{-- Banner: cliente encontrado na base --}}
    @if($pessoaExistenteId)
        <div class="mb-6 flex items-start gap-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 rounded-2xl p-4">
            <div class="shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-800/40 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-emerald-800 dark:text-emerald-300">Cliente já cadastrado — dados carregados</p>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">Os dados foram preenchidos automaticamente. Um <strong>novo processo</strong> será vinculado a este cliente ao finalizar.</p>
            </div>
            <button
                wire:click="$set('pessoaExistenteId', null)"
                class="shrink-0 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 transition uppercase tracking-widest"
            >
                Limpar
            </button>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-900 p-8 lg:p-12 rounded-[2rem] border border-gray-100 dark:border-zinc-800 shadow-xl shadow-gray-200/50 dark:shadow-none">
        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    </div>

    <div class="mt-8 flex justify-center lg:justify-start">
        <a href="{{ route('dashboard') }}" wire:navigate class="text-xs font-bold text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors uppercase tracking-widest">
            ← Voltar ao Dashboard
        </a>
    </div>
</div>
