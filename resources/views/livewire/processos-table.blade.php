<div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-zinc-50">Gestão de Processos</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Controle total de prazos, mérito e performance processual.</p>
        </div>
    </div>

    {{ $this->table }}

    <x-filament-actions::modals />
</div>