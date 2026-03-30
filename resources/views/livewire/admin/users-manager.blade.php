<div class="space-y-6">
    <header class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Gestão de Usuários</h1>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em]">K&C Analytics • Administradores e Equipe</p>
        </div>
    </header>

    <div class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-300 dark:border-zinc-800 rounded-3xl p-6">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>
