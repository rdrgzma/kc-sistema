<header
    class="h-24 bg-white dark:bg-zinc-900 flex items-center justify-between pl-10 pr-2 transition-all border-b border-slate-300 dark:border-zinc-800">
    <div class="flex flex-col">
        <h1 class="text-2xl font-black text-slate-900 dark:text-zinc-50 tracking-tight leading-none">
            {{ $headerTitle ?? 'Painel Estratégico' }}
        </h1>
        <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em]">
            {{ $headerSubtitle ?? (auth()->user()?->escritorio?->nome ?? 'K&C Sistema Jurídico') }}
        </p>
    </div>

    <div class="flex items-center gap-10">
        {{-- Barra de Busca Global --}}
        <livewire:global-search />

        <div class="ml-auto flex items-center gap-4">
            <button @click="$store.theme.toggle()"
                class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-2xl text-slate-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all outline-none cursor-pointer border border-slate-300 dark:border-zinc-700 hover:border-primary-300 active:scale-95 shadow-sm"
                title="Alternar tema">
                <svg x-show="!$store.theme.isDark" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>
                <svg x-show="$store.theme.isDark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </button>

            <div class="h-8 w-px bg-slate-300 dark:bg-zinc-800 mx-2"></div>

            <div x-data="{ userMenuOpen: false }" class="relative">
                <button @click="userMenuOpen = !userMenuOpen"
                    class="flex items-center gap-4 text-right cursor-pointer group focus:outline-none">
                    <div class="hidden lg:block text-right">
                        <p
                            class="text-sm font-black text-slate-900 dark:text-zinc-50 leading-tight group-hover:text-primary-600 transition-colors">
                            {{ auth()->user()?->name ?? 'Usuário' }}
                        </p>
                        <p
                            class="text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-widest mt-1">
                            {{ auth()->user()?->roles->first()?->name ?? 'Sem Papel' }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-2xl bg-slate-900 dark:bg-zinc-100 text-white dark:text-slate-900 flex items-center justify-center font-black text-sm shadow-xl shadow-slate-900/10 border border-slate-900 dark:border-zinc-500 group-hover:scale-105 transition-transform">
                        {{ auth()->user() ? auth()->user()->initials() : 'US' }}
                    </div>
                </button>

                <!-- Dropdown Menu Customizado -->
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                    class="absolute right-0 mt-4 w-64 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl shadow-2xl z-50 overflow-hidden p-2"
                    x-cloak>

                    <a href="{{ route('profile.edit') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-2xl transition-colors group">
                        <x-heroicon-o-cog
                            class="w-5 h-5 text-slate-400 group-hover:text-primary-600 transition-colors" />
                        Configurações do Perfil
                    </a>

                    <div class="h-px bg-slate-100 dark:bg-zinc-800 my-2 mx-4"></div>

                    <button @click="userMenuOpen = false; $dispatch('open-logout-modal')"
                        class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-2xl transition-colors text-left group">
                        <x-heroicon-o-arrow-right-start-on-rectangle
                            class="w-5 h-5 text-red-400 group-hover:text-red-600 transition-colors" />
                        Sair do Sistema
                    </button>
                </div>
            </div>
        </div>
    </div>
    <x-logout-modal />
</header>