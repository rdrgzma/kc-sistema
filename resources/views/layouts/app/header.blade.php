<header class="h-24 bg-white dark:bg-zinc-900 flex items-center justify-between px-10 transition-colors border-b border-transparent dark:border-zinc-800">
    <div class="flex flex-col">
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-zinc-50 tracking-tight leading-none">{{ $headerTitle ?? 'Painel Estratégico' }}</h1>
        <p class="text-xs font-semibold text-gray-400 dark:text-zinc-500 mt-1 uppercase tracking-widest">{{ $headerSubtitle ?? 'Silva & Associados' }}</p>
    </div>

    <div class="flex items-center gap-8">
        <div class="relative flex items-center">
            <input type="text" 
                   placeholder="Buscar..." 
                   class="w-64 bg-gray-50 dark:bg-zinc-800 border-none rounded-xl px-5 py-2.5 text-sm text-gray-600 dark:text-zinc-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
        </div>

        <button @click="toggleTheme()" class="p-2.5 bg-gray-50 dark:bg-zinc-800 rounded-xl text-gray-500 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors outline-none cursor-pointer">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
        
        <div class="flex items-center gap-4 text-right">
            <div>
                <p class="text-sm font-bold text-gray-900 dark:text-zinc-50 leading-tight">Dr. Carlos Silva</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Proprietário</p>
            </div>
        </div>
    </div>
</header>


