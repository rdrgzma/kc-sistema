<div class="flex flex-col h-[500px]">
    
    <div class="flex-1 overflow-y-auto pr-4 space-y-4 custom-scrollbar mb-4">
        @forelse($comentarios as $comentario)
            <div class="flex space-x-3">
                <div class="flex-shrink-0">
                    <x-flux::icon.user-circle class="w-8 h-8 text-gray-400" />
                </div>
                
                <div class="bg-gray-100 dark:bg-zinc-800 rounded-lg rounded-tl-none p-3 max-w-[85%]">
                    <div class="flex items-center justify-between space-x-4 mb-1">
                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                            {{ $comentario->user->name ?? 'Utilizador' }}
                        </span>
                        <span class="text-[10px] text-gray-500">
                            {{ $comentario->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $comentario->content }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500 text-sm">
                Ainda não há comentários nesta tarefa.
            </div>
        @endforelse
    </div>

    <div class="mt-auto border-t border-gray-200 dark:border-zinc-700 pt-4">
        <form wire:submit="adicionarComentario" class="flex gap-2">
            <div class="flex-1">
                <textarea 
                    wire:model="novoComentario" 
                    placeholder="Escreva um comentário ou atualização..." 
                    class="w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    rows="2"
                    required
                ></textarea>
            </div>
            <button 
                type="submit" 
                class="self-end inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none transition ease-in-out duration-150"
            >
                Enviar
            </button>
        </form>
    </div>
</div>