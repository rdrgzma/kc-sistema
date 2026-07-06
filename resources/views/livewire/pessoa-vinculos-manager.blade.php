<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-black text-slate-900 dark:text-zinc-50 tracking-tight">
                {{ $this->pessoa->tipo === 'PF' ? 'Empresas Vinculadas (Pessoa Jurídica)' : 'Representantes/Sócios Vinculados (Pessoa Física)' }}
            </h3>
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-wider mt-1">
                Gerencie os vínculos deste cliente com outras pessoas físicas ou jurídicas cadastradas
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>
