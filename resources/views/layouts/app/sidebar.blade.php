<aside class="w-64 bg-white dark:bg-zinc-900 border-r border-gray-100 dark:border-zinc-800 flex flex-col shrink-0 transition-colors">
    <div class="h-20 flex items-center px-8">
        <span class="text-lg font-bold text-gray-900 dark:text-zinc-50 flex items-center gap-3">
            <span class="text-2xl">⚖️</span>
            K&C Analytics
        </span>
    </div>
    
    <nav class="flex-1 px-4 py-4 space-y-1">
        <a href="{{ route('dashboard') }}" 
           wire:navigate
           class="flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
            Dashboard
        </a>

        <a href="{{ route('pessoas.index') }}" 
           wire:navigate
           class="flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('pessoas.*') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
            Pessoas
        </a>

        <a href="{{ route('processos.index') }}" 
           wire:navigate
           class="flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('processos.*') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
            Processos
        </a>
    </nav>
</aside>