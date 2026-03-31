<div x-data="{ open: false }" 
     @open-logout-modal.window="open = true" 
     class="relative z-[9999]" 
     x-show="open" 
     x-cloak>
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-zinc-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-zinc-800">
                
                <div class="px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-black leading-6 text-slate-900 dark:text-zinc-50" id="modal-title">Sair do Sistema?</h3>
                            <div class="mt-2 text-sm font-bold text-slate-500 dark:text-zinc-400">
                                <p>Você tem certeza que deseja encerrar sua sessão atual no KC-Sistema? Todos os dados não salvos podem ser perdidos.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-zinc-800/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 rounded-b-3xl">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-2xl bg-red-600 px-6 py-2.5 text-sm font-black text-white shadow-sm hover:bg-red-500 sm:w-auto transition-all active:scale-95">
                            Encerrar Sessão
                        </button>
                    </form>
                    <button type="button" 
                            @click="open = false"
                            class="mt-3 inline-flex w-full justify-center rounded-2xl bg-white dark:bg-zinc-800 px-6 py-2.5 text-sm font-black text-slate-900 dark:text-zinc-100 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 sm:mt-0 sm:w-auto transition-all active:scale-95">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
