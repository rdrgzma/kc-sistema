<header class="h-24 bg-white dark:bg-zinc-900 flex items-center justify-between px-10 transition-all border-b border-slate-300 dark:border-zinc-800 relative z-40">
    <div class="flex flex-col">
        <h1 class="text-2xl font-black text-slate-900 dark:text-zinc-50 tracking-tight leading-none">{{ $headerTitle ?? 'Painel Estratégico' }}</h1>
        <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em]">{{ $headerSubtitle ?? (auth()->user()?->escritorio?->nome ?? 'K&C Analytics') }}</p>
    </div>

    <div class="flex items-center gap-10">
        <div class="relative flex items-center group">
            <div class="absolute inset-x-0 bottom-0 h-0.5 bg-primary-600 scale-x-0 group-focus-within:scale-x-100 transition-transform origin-left rounded-full shadow-[0_0_10px_rgba(37,99,235,0.3)]"></div>
            <x-flux::icon.magnifying-glass class="w-5 h-5 absolute left-0 text-slate-400 dark:text-zinc-500 group-focus-within:text-primary-600 transition-colors" />
            <input type="text" 
                   placeholder="Buscar processos, pessoas..." 
                   class="w-80 bg-transparent dark:bg-transparent border-none px-8 py-3 text-sm font-bold text-slate-700 dark:text-zinc-300 placeholder-slate-400 focus:ring-0 transition-all outline-none italic tracking-wide">
        </div>

        <div class="flex items-center gap-4">
            <button @click="toggleTheme()" class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-2xl text-slate-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all outline-none cursor-pointer border border-slate-300 dark:border-zinc-700 hover:border-primary-300 active:scale-95 shadow-sm">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            
            <div class="h-8 w-px bg-slate-300 dark:bg-zinc-800 mx-2"></div>

            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-4 text-right cursor-pointer group">
                <div class="hidden lg:block">
                    <p class="text-sm font-black text-slate-900 dark:text-zinc-50 leading-tight group-hover:text-primary-600 transition-colors">{{ auth()->user()?->name ?? 'Usuário' }}</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-widest mt-1">{{ auth()->user()?->roles->first()?->name ?? 'Sem Papel' }}</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-slate-900 dark:bg-zinc-100 text-white dark:text-slate-900 flex items-center justify-center font-black text-sm shadow-xl shadow-slate-900/10 border border-slate-900 dark:border-zinc-500">
                    {{ auth()->user() ? auth()->user()->initials() : 'US' }}
                </div>
            </a>
        </div>
    </div>
</header>
