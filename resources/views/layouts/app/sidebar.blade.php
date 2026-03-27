<aside :class="isSidebarOpen ? 'w-64' : 'w-20'" class="bg-white dark:bg-zinc-900 border-r border-gray-100 dark:border-zinc-800 flex flex-col shrink-0 transition-all duration-300 relative">
    
    <!-- Botão Recolher/Expandir -->
    <button @click="toggleSidebar()" class="absolute -right-3 top-7 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-full p-1 shadow text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors z-50" title="Alternar visão menu">
        <svg x-show="isSidebarOpen" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <svg x-show="!isSidebarOpen" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div class="h-20 flex items-center px-6 overflow-hidden">
        <span class="text-lg font-bold text-gray-900 dark:text-zinc-50 flex items-center gap-3 whitespace-nowrap shrink-0">
            <span class="text-2xl mt-0.5">⚖️</span>
            <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms>K&C Analytics</span>
        </span>
    </div>
    
    <nav class="flex-1 px-4 py-4 flex flex-col gap-2">
        <div class="group relative">
            <a href="{{ route('dashboard') }}" 
               wire:navigate
               class="flex items-center p-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-chart-pie class="w-6 h-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500' }}" />
                <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms class="ml-3 overflow-hidden whitespace-nowrap">Dashboard</span>
            </a>
            <!-- Tooltip Hover -->
            <div x-show="!isSidebarOpen" class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-gray-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-bold rounded shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                Dashboard
                <!-- Flecha lateral do tooltip -->
                <div class="absolute w-2 h-2 bg-gray-900 dark:bg-zinc-100 transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
            </div>
        </div>

        <div class="group relative">
            <a href="{{ route('pessoas.index') }}" 
               wire:navigate
               class="flex items-center p-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('pessoas.*') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-users class="w-6 h-6 shrink-0 {{ request()->routeIs('pessoas.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500' }}" />
                <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms class="ml-3 overflow-hidden whitespace-nowrap">Pessoas</span>
            </a>
            <div x-show="!isSidebarOpen" class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-gray-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-bold rounded shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                Pessoas
                <div class="absolute w-2 h-2 bg-gray-900 dark:bg-zinc-100 transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
            </div>
        </div>

        <div class="group relative">
            <a href="{{ route('processos.index') }}" 
               wire:navigate
               class="flex items-center p-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('processos.*') ? 'text-blue-600 dark:text-blue-400 bg-gray-50/50 dark:bg-zinc-800/50' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-folder-open class="w-6 h-6 shrink-0 {{ request()->routeIs('processos.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500' }}" />
                <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms class="ml-3 overflow-hidden whitespace-nowrap">Processos</span>
            </a>
            <div x-show="!isSidebarOpen" class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-gray-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-bold rounded shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                Processos
                <div class="absolute w-2 h-2 bg-gray-900 dark:bg-zinc-100 transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
            </div>
        </div>

        <div class="pt-4 pb-2 px-2 overflow-hidden flex" :class="!isSidebarOpen && 'justify-center'">
            <div x-show="isSidebarOpen" class="h-px bg-gray-200 dark:bg-zinc-800 flex-1 my-auto mr-2"></div>
            <p x-show="isSidebarOpen" class="text-[10px] whitespace-nowrap font-black uppercase text-gray-400 dark:text-zinc-500 tracking-widest leading-none">Ferramentas</p>
            <div x-show="!isSidebarOpen" class="h-px w-6 bg-gray-200 dark:bg-zinc-800"></div>
        </div>

        <div class="group relative">
            <a href="{{ route('onboarding') }}" 
               wire:navigate
               class="flex items-center p-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('onboarding') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/20' : 'text-gray-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30' }}">
                <x-heroicon-o-rocket-launch class="w-6 h-6 shrink-0 {{ request()->routeIs('onboarding') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-zinc-500' }}" />
                <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms class="ml-3 overflow-hidden whitespace-nowrap">Fluxo Onboarding</span>
            </a>
            <div x-show="!isSidebarOpen" class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-gray-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-bold rounded shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                Fluxo Onboarding
                <div class="absolute w-2 h-2 bg-gray-900 dark:bg-zinc-100 transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
            </div>
        </div>
    </nav>
</aside>