<div class="space-y-1" style="margin-left: {{ $level * 1 }}rem">
    <button wire:click="goToFolder({{ $item->id }})" 
        class="flex items-center gap-3 w-full text-left p-2.5 rounded-xl transition-all duration-300 {{ $current_folder_id == $item->id ? 'bg-primary-50 dark:bg-zinc-800 text-primary-600 font-black border-l-4 border-primary-600' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
        <x-heroicon-s-folder class="w-4 h-4 {{ $current_folder_id == $item->id ? 'text-primary-600' : 'text-amber-400' }}"/>
        <span class="text-[11px] font-bold uppercase tracking-wider truncate">{{ $item->nome }}</span>
    </button>
    
    @if($item->subpastas->isNotEmpty())
        <div class="ml-2 border-l border-slate-100 dark:border-zinc-800 pl-2 space-y-1">
            @foreach($item->subpastas as $sub)
                @include('livewire.document-manager-tree-item', ['item' => $sub, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
