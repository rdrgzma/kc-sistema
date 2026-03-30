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
            <button @click="$store.theme.toggle()" class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-2xl text-slate-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all outline-none cursor-pointer border border-slate-300 dark:border-zinc-700 hover:border-primary-300 active:scale-95 shadow-sm" title="Alternar tema">
                <svg x-show="!$store.theme.isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="$store.theme.isDark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            
            <div class="h-8 w-px bg-slate-300 dark:bg-zinc-800 mx-2"></div>

            <div x-data="{ userMenuOpen: false }" class="relative">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-4 text-right cursor-pointer group focus:outline-none">
                    <div class="hidden lg:block text-right">
                        <p class="text-sm font-black text-slate-900 dark:text-zinc-50 leading-tight group-hover:text-primary-600 transition-colors">{{ auth()->user()?->name ?? 'Usuário' }}</p>
                        <p class="text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-widest mt-1">{{ auth()->user()?->roles->first()?->name ?? 'Sem Papel' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 dark:bg-zinc-100 text-white dark:text-slate-900 flex items-center justify-center font-black text-sm shadow-xl shadow-slate-900/10 border border-slate-900 dark:border-zinc-500 group-hover:scale-105 transition-transform">
                        {{ auth()->user() ? auth()->user()->initials() : 'US' }}
                    </div>
                </button>

                <!-- Dropdown Menu Customizado -->
                <div x-show="userMenuOpen" 
                     @click.away="userMenuOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="absolute right-0 mt-4 w-64 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl shadow-2xl z-50 overflow-hidden p-2"
                     x-cloak>
                    
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-2xl transition-colors group">
                        <x-heroicon-o-cog class="w-5 h-5 text-slate-400 group-hover:text-primary-600 transition-colors" />
                        Configurações do Perfil
                    </a>
                    
                    <div class="h-px bg-slate-100 dark:bg-zinc-800 my-2 mx-4"></div>

                    <button @click="userMenuOpen = false; $dispatch('open-logout-modal')" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-2xl transition-colors text-left group">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5 text-red-400 group-hover:text-red-600 transition-colors" />
                        Sair do Sistema
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Modal de Logout Customizado (Tailwind + Alpine) -->
<div x-data="{ open: false }" 
     @open-logout-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    
    <!-- Backdrop: suave no claro, denso no escuro -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/30 dark:bg-zinc-950/80 backdrop-blur-sm transition-opacity" 
         @click="open = false">
    </div>

    <!-- Conteúdo do Modal -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative z-10 bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl shadow-slate-200 dark:shadow-zinc-950/60 w-full max-w-md overflow-hidden border border-slate-200 dark:border-zinc-700">
        
        <!-- Cabeçalho do Modal -->
        <div class="p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500 dark:text-red-400 border border-red-200 dark:border-red-800/50 shrink-0">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-zinc-50 tracking-tight leading-none">Confirmar Saída</h3>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-[0.2em] mt-1.5">K&C Analytics • Segurança</p>
                </div>
            </div>

            <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed border-l-2 border-red-200 dark:border-red-700 pl-4">
                Deseja encerrar sua sessão? Todos os dados não salvos podem ser perdidos. Sua sessão será finalizada com segurança.
            </p>
        </div>

        <!-- Rodapé com botões -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-zinc-800/60 flex items-center gap-3 justify-end border-t border-slate-100 dark:border-zinc-700/60">
            <button @click="open = false"
                    class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-100 rounded-xl hover:bg-white dark:hover:bg-zinc-700 border border-transparent hover:border-slate-200 dark:hover:border-zinc-600 transition-all">
                Voltar
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-red-600/25 hover:shadow-red-600/40 active:scale-95 transition-all">
                    Encerrar Sessão
                </button>
            </form>
        </div>
    </div>
</div>
