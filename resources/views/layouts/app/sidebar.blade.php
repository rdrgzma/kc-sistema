<aside :class="isSidebarOpen ? 'w-64' : 'w-20'" class="bg-white dark:bg-zinc-900 border-r border-slate-300 dark:border-zinc-800 flex flex-col shrink-0 transition-all duration-300 relative z-30 shadow-none">
    
    <!-- Botão Recolher/Expandir -->
    <button @click="toggleSidebar()" class="absolute -right-3 top-7 bg-white dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 rounded-full p-1 shadow-md text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all active:scale-95 z-40" title="Alternar visão menu">
        <svg x-show="isSidebarOpen" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <svg x-show="!isSidebarOpen" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div class="h-24 flex items-center px-6 overflow-hidden">
        <span class="text-xl font-black text-slate-900 dark:text-zinc-50 flex items-center gap-4 whitespace-nowrap shrink-0 tracking-tight">
            <span class="text-3xl">⚖️</span>
            <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms>K&C Analytics</span>
        </span>
    </div>
    
    <nav class="flex-1 px-4 py-6 flex flex-col gap-3">
        @php
            $navItems = [
                ['route' => 'dashboard', 'icon' => 'heroicon-o-chart-pie', 'label' => 'Dashboard', 'active' => request()->routeIs('dashboard')],
                ['route' => 'pessoas.index', 'icon' => 'heroicon-o-users', 'label' => 'Pessoas', 'active' => request()->routeIs('pessoas.*')],
                ['route' => 'processos.index', 'icon' => 'heroicon-o-folder-open', 'label' => 'Processos', 'active' => request()->routeIs('processos.*')],
                ['type' => 'divider', 'label' => 'Ferramentas'],
                ['route' => 'onboarding', 'icon' => 'heroicon-o-rocket-launch', 'label' => 'Fluxo Onboarding', 'active' => request()->routeIs('onboarding'), 'accent' => 'indigo'],
                ['route' => 'planners.index', 'icon' => 'heroicon-o-calendar', 'label' => 'Planners', 'active' => request()->routeIs('planners.*'), 'accent' => 'primary'],
            ];

            if (auth()->user()?->hasAnyRole(['Administrador', 'Sócio'])) {
                $navItems[] = ['type' => 'divider', 'label' => 'Administração'];
                $navItems[] = ['route' => 'admin.users', 'icon' => 'heroicon-o-user-group', 'label' => 'Usuários', 'active' => request()->routeIs('admin.users')];
                $navItems[] = ['route' => 'admin.escritorios', 'icon' => 'heroicon-o-building-office-2', 'label' => 'Escritórios', 'active' => request()->routeIs('admin.escritorios')];
                $navItems[] = ['route' => 'admin.equipes', 'icon' => 'heroicon-o-rectangle-group', 'label' => 'Equipes', 'active' => request()->routeIs('admin.equipes')];
                $navItems[] = ['route' => 'admin.peritos', 'icon' => 'heroicon-o-academic-cap', 'label' => 'Peritos', 'active' => request()->routeIs('admin.peritos')];
                $navItems[] = ['route' => 'admin.assistentes', 'icon' => 'heroicon-o-briefcase', 'label' => 'Assistentes', 'active' => request()->routeIs('admin.assistentes')];
                $navItems[] = ['route' => 'admin.especialidades', 'icon' => 'heroicon-o-tag', 'label' => 'Especialidades', 'active' => request()->routeIs('admin.especialidades')];
                $navItems[] = ['route' => 'admin.fases', 'icon' => 'heroicon-o-adjustments-horizontal', 'label' => 'Fases', 'active' => request()->routeIs('admin.fases')];
            }
        @endphp

        @foreach($navItems as $item)
            @if(isset($item['type']) && $item['type'] === 'divider')
                <div class="pt-8 pb-3 px-2 overflow-hidden flex" :class="!isSidebarOpen && 'justify-center'">
                    <div x-show="isSidebarOpen" class="h-px bg-slate-200 dark:bg-zinc-800 flex-1 my-auto mr-3"></div>
                    <p x-show="isSidebarOpen" class="text-[10px] whitespace-nowrap font-black uppercase text-slate-500 dark:text-zinc-500 tracking-[0.2em] leading-none">{{ $item['label'] }}</p>
                    <div x-show="!isSidebarOpen" class="h-px w-6 bg-slate-200 dark:bg-zinc-800"></div>
                </div>
            @else
                <div class="group relative">
                    <a href="{{ route($item['route']) }}" 
                       wire:navigate
                       class="flex items-center p-2.5 text-sm font-bold rounded-xl transition-all border {{ $item['active'] ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border-primary-200/60 dark:border-primary-800/40' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-100 hover:bg-slate-50 dark:hover:bg-zinc-800 border-transparent hover:border-slate-200 dark:hover:border-zinc-700' }}">
                        <x-dynamic-component :component="$item['icon']" class="w-6 h-6 shrink-0 {{ $item['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-slate-500 dark:text-zinc-500 group-hover:text-slate-900' }}" />
                        <span x-show="isSidebarOpen" x-transition.opacity.duration.300ms class="ml-4 overflow-hidden whitespace-nowrap tracking-wide">{{ $item['label'] }}</span>
                    </a>
                    
                    <div x-show="!isSidebarOpen" class="absolute left-full ml-4 top-1/2 -translate-y-1/2 px-3 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                        {{ $item['label'] }}
                        <div class="absolute w-2 h-2 bg-slate-900 dark:bg-white transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
                    </div>
                </div>
            @endif
        @endforeach
    </nav>
</aside>