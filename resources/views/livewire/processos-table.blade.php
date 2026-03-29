<div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Gestão de Processos</h2>
            <p class="text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em] mt-2 italic">Controle total de prazos, mérito e performance processual</p>
        </div>
    </div>

    <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-zinc-800">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>