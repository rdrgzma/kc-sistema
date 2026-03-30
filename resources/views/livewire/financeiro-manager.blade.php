<div class="space-y-6">
    <div class="bg-blue-50/30 p-5 rounded-xl border border-blue-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
        <h5 class="text-[10px] font-bold uppercase text-blue-500 mb-3 tracking-widest">Novo Lançamento</h5>
        {{ $this->form }}
        <button wire:click="registrar" class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition shadow-md shadow-blue-200">
            Adicionar ao Extrato
        </button>
    </div>

    <div class="mt-6">
        {{ $this->table }}
    </div>
</div>