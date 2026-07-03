<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Novo Cálculo</h2>
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Salvar Cálculo
                </x-filament::button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{ $this->table }}
    </div>
</div>
