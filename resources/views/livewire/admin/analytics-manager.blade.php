<div class="space-y-8">
    {{-- Header --}}
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-200 dark:border-zinc-800">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">Analytics & Auditoria</h1>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 mt-2 uppercase tracking-[0.2em]">Histórico de ações e logs de alteração dos colaboradores</p>
        </div>
    </header>

    {{-- Tabela --}}
    <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest italic">Logs do Sistema</h2>
        </div>
        
        <div class="overflow-x-auto">
            {{ $this->table }}
        </div>
    </div>

    <x-filament-actions::modals />
</div>
