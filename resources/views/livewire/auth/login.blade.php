<div class="mt-8 pt-8 border-t border-slate-100 dark:border-zinc-800">
    <div class="text-center mb-4">
        <h2 class="text-sm font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">
            {{ __('Atalhos de Desenvolvimento') }}
        </h2>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">
            {{ __('Acesso rápido sem senha (apenas local)') }}
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-3 text-xs font-bold text-center text-red-700 bg-red-50 rounded-xl dark:bg-red-900/30 dark:text-red-300 border border-red-100 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-2 gap-3">
        <button type="button" wire:click="loginAs('admin@teste.com')" wire:loading.attr="disabled"
            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-dashed border-red-200 dark:border-red-900/50 bg-red-50/30 dark:bg-red-950/10 hover:bg-red-50 dark:hover:bg-red-950/20 hover:border-red-300 dark:hover:border-red-800 text-red-700 dark:text-red-400 transition-all group cursor-pointer">
            <span class="text-xs font-black uppercase tracking-wider">{{ __('Admin') }}</span>
            <span class="text-[10px] text-red-500/80 dark:text-red-500 font-medium mt-0.5">{{ __('admin@teste.com') }}</span>
        </button>

        <button type="button" wire:click="loginAs('socio@teste.com')" wire:loading.attr="disabled"
            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-dashed border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/10 hover:bg-amber-50 dark:hover:bg-amber-950/20 hover:border-amber-300 dark:hover:border-amber-800 text-amber-700 dark:text-amber-400 transition-all group cursor-pointer">
            <span class="text-xs font-black uppercase tracking-wider">{{ __('Sócio') }}</span>
            <span class="text-[10px] text-amber-500/80 dark:text-amber-500 font-medium mt-0.5">{{ __('socio@teste.com') }}</span>
        </button>

        <button type="button" wire:click="loginAs('processos@teste.com')" wire:loading.attr="disabled"
            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-dashed border-blue-200 dark:border-blue-900/50 bg-blue-50/30 dark:bg-blue-950/10 hover:bg-blue-50 dark:hover:bg-blue-950/20 hover:border-blue-300 dark:hover:border-blue-800 text-blue-700 dark:text-blue-400 transition-all group cursor-pointer">
            <span class="text-xs font-black uppercase tracking-wider">{{ __('Processos') }}</span>
            <span class="text-[10px] text-blue-500/80 dark:text-blue-500 font-medium mt-0.5">{{ __('processos@teste.com') }}</span>
        </button>

        <button type="button" wire:click="loginAs('gr@teste.com')" wire:loading.attr="disabled"
            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-dashed border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/30 dark:bg-emerald-950/10 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 hover:border-emerald-300 dark:hover:border-emerald-800 text-emerald-700 dark:text-emerald-400 transition-all group cursor-pointer">
            <span class="text-xs font-black uppercase tracking-wider">{{ __('GR') }}</span>
            <span class="text-[10px] text-emerald-500/80 dark:text-emerald-500 font-medium mt-0.5">{{ __('gr@teste.com') }}</span>
        </button>
    </div>
</div>
