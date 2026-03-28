<div class="space-y-4">
    <div class="bg-blue-50/50 p-4 rounded-xl border border-dashed border-blue-200 dark:bg-zinc-900 dark:border-zinc-800">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 gap-2">
        @forelse($this->model->documentos()->latest()->get() as $doc)
            <div class="flex items-center justify-between p-3 bg-white border rounded-lg shadow-sm group hover:border-blue-300 transition-colors">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="p-2 bg-gray-100 rounded text-gray-500 uppercase text-[10px] font-bold">
                        {{ $doc->extensao }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-700 truncate max-w-[150px] sm:max-w-xs">
                            {{ $doc->nome_arquivo }}
                        </span>
                        <span class="text-[10px] text-gray-400">
                            {{ number_format($doc->tamanho / 1024, 2) }} KB
                        </span>
                    </div>
                </div>
                
                <div class="flex gap-1">
                    <a href="{{ Storage::url($doc->caminho) }}" target="_blank" 
                       class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition" title="Download">
                        <x-heroicon-m-arrow-down-tray class="w-4 h-4"/>
                    </a>
                    
                    <button wire:click="excluirDocumento({{ $doc->id }})" 
                            wire:confirm="Tem certeza que deseja excluir este arquivo permanentemente?"
                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition" title="Excluir">
                        <x-heroicon-m-trash class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-8 border-2 border-dashed border-gray-100 rounded-xl">
                <p class="text-xs text-gray-400 italic">Nenhum documento anexado ainda.</p>
            </div>
        @endforelse
    </div>
</div>