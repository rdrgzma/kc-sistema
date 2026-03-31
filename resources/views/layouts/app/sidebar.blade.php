<aside :class="isSidebarOpen ? 'w-64' : 'w-20'"
    class="bg-white dark:bg-zinc-900 border-r border-slate-300 dark:border-zinc-800 flex flex-col shrink-0 transition-all duration-300 shadow-none">



    <div class="h-24 flex items-center justify-between px-6 overflow-hidden relative">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-4 shrink-0 transition-transform active:scale-95">
            <span class="text-3xl">⚖️</span>
            <div x-show="isSidebarOpen" class="flex flex-col">
                <span class="text-xl font-black text-slate-900 dark:text-white leading-none tracking-tighter uppercase font-sans">
                    {{ explode(' ', config('app.name'), 2)[0] }}
                </span>
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.25em] mt-1.5 whitespace-nowrap opacity-90 font-sans">
                    {{ explode(' ', config('app.name'), 2)[1] ?? '' }}
                </span>
            </div>
        </a>

        <!-- Botão de Retração Original -->
        <button @click="toggleSidebar()"
            class="absolute -right-3 top-8 w-6 h-6 bg-slate-200 dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 rounded-full flex items-center justify-center text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all shadow-sm z-50 hover:scale-110 active:scale-95 outline-none">
            <x-heroicon-o-chevron-left x-show="isSidebarOpen" class="w-3.5 h-3.5" />
            <x-heroicon-o-chevron-right x-show="!isSidebarOpen" class="w-3.5 h-3.5" />
        </button>
    </div>

    <nav :class="isSidebarOpen ? 'overflow-y-auto' : 'overflow-visible'"
         class="flex-1 px-4 py-8 flex flex-col gap-10 scrollbar-hide">
        @php
            $sections = [
                [
                    'label' => 'Operacional',
                    'items' => [
                        ['route' => 'dashboard', 'icon' => 'heroicon-o-chart-pie', 'label' => 'Início'],
                        ['route' => 'processos.index', 'icon' => 'heroicon-o-scale', 'label' => 'Processos'],
                        ['route' => 'pessoas.index', 'icon' => 'heroicon-o-user-circle', 'label' => 'Clientes'],
                        ['route' => 'financeiro.index', 'icon' => 'heroicon-o-banknotes', 'label' => 'Financeiro'],
                    ]
                ],
                [
                    'label' => 'Planejamento',
                    'items' => [
                        ['route' => 'onboarding', 'icon' => 'heroicon-o-rocket-launch', 'label' => 'Onboarding'],
                        ['route' => 'planners.index', 'icon' => 'heroicon-o-presentation-chart-line', 'label' => 'Planners'],
                    ]
                ],
                [
                    'label' => 'Sistema',
                    'roles' => ['Administrador', 'Sócio'],
                    'items' => [
                        ['route' => 'admin.users', 'icon' => 'heroicon-o-user-group', 'label' => 'Usuários'],
                        ['route' => 'admin.escritorios', 'icon' => 'heroicon-o-building-office-2', 'label' => 'Escritórios'],
                        ['route' => 'admin.equipes', 'icon' => 'heroicon-o-rectangle-group', 'label' => 'Equipes'],
                        ['route' => 'admin.peritos', 'icon' => 'heroicon-o-academic-cap', 'label' => 'Peritos'],
                        ['route' => 'admin.assistentes', 'icon' => 'heroicon-o-briefcase', 'label' => 'Assistentes'],
                        ['route' => 'admin.especialidades', 'icon' => 'heroicon-o-tag', 'label' => 'Especialidades'],
                        ['route' => 'admin.fases', 'icon' => 'heroicon-o-adjustments-horizontal', 'label' => 'Fases'],
                    ]
                ]
            ];
        @endphp

        @foreach ($sections as $section)
            @if (!isset($section['roles']) || auth()->user()?->hasAnyRole($section['roles']))
                @php
                    $isAnyItemActive = collect($section['items'])->contains(fn($item) => request()->routeIs($item['route'] . '*'));
                @endphp

                <div x-data="{ expanded: {{ $isAnyItemActive ? 'true' : 'false' }} }" class="flex flex-col gap-1 focus:outline-none">
                    @if ($section['label'])
                        <button @click="expanded = !expanded"
                            class="mt-6 mb-2 px-4 flex items-center gap-3 w-full text-left outline-none group/section">
                            <span x-show="isSidebarOpen" class="text-[9px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-[0.3em] font-sans whitespace-nowrap group-hover/section:text-slate-600">
                                {{ $section['label'] }}
                            </span>
                            <div class="h-px bg-slate-100 dark:bg-zinc-800/60 flex-1"></div>
                            <x-heroicon-o-chevron-down x-show="isSidebarOpen"
                                class="w-2.5 h-2.5 text-slate-300 transition-transform duration-200 group-hover/section:text-slate-500"
                                x-bind:class="expanded ? 'rotate-180' : ''" />
                        </button>
                    @endif

                    <div x-show="!isSidebarOpen || expanded"
                        :class="isSidebarOpen ? 'overflow-hidden' : ''"
                        class="flex flex-col gap-0.5 transition-all duration-300">
                        @foreach ($section['items'] as $item)
                            @php
                                $isActive = request()->routeIs($item['route'] . '*');
                            @endphp
                            <div class="relative px-1">
                                <a href="{{ route($item['route']) }}" wire:navigate
                                    class="group/item flex items-center justify-center lg:justify-start px-3 py-2 text-sm font-bold rounded-xl transition-all border {{ $isActive ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border-primary-100 dark:border-primary-800/40' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-100 hover:bg-slate-50 dark:hover:bg-zinc-800 border-transparent hover:border-slate-100 dark:hover:border-zinc-700' }}">

                                    <x-dynamic-component :component="$item['icon']"
                                        class="w-5 h-5 shrink-0 transition-colors {{ $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-zinc-600 group-hover/item:text-slate-900 dark:group-hover/item:text-zinc-100' }}" />

                                    <span x-show="isSidebarOpen" class="ml-3.5 overflow-hidden whitespace-nowrap tracking-tight">
                                        {{ $item['label'] }}
                                    </span>

                                    <!-- Tip / Tooltip flutuante quando recolhido -->
                                    <div x-show="!isSidebarOpen"
                                        class="absolute left-full ml-4 top-1/2 -translate-y-1/2 px-3 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-2xl opacity-0 invisible group-hover/item:opacity-100 group-hover/item:visible transition-all pointer-events-none z-50 whitespace-nowrap">
                                        {{ $item['label'] }}
                                        <div class="absolute w-2 h-2 bg-slate-900 dark:bg-white transform rotate-45 -left-1 top-1/2 -translate-y-1/2"></div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>
</aside>