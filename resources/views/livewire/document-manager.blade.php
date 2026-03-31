<div class="space-y-6">
    {{-- Header de Operações --}}
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-zinc-900 p-5 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-500">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary-50 dark:bg-zinc-800 rounded-2xl transition-colors">
                <x-heroicon-o-folder-open class="w-6 h-6 text-primary-600 dark:text-primary-400"/>
            </div>
            <div>
                <h4 class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-400 dark:text-zinc-500">Gestão Estratégica de Documentos</h4>
                <div class="flex items-center gap-2 mt-0.5">
                    <button wire:click="goToFolder(null)" class="text-sm font-black text-slate-900 dark:text-gray-200 hover:text-primary-600 transition-colors">GED Raiz</button>
                    @foreach($breadcrumb as $crumb)
                        <x-heroicon-m-chevron-right class="w-3 h-3 text-slate-400"/>
                        <button wire:click="goToFolder({{ $crumb->id }})" class="text-sm font-black text-slate-900 dark:text-gray-200 hover:text-primary-600 transition-colors">{{ $crumb->nome }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{ $this->createFolderAction() }}
            {{ $this->uploadAction() }}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Sidebar de Árvore --}}
        <aside class="lg:col-span-3 space-y-4">
            <div class="bg-slate-50/50 dark:bg-zinc-900/50 p-6 rounded-3xl border border-slate-100 dark:border-zinc-800 backdrop-blur-md">
                <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                    <x-heroicon-o-bars-3-bottom-left class="w-4 h-4"/>
                    Navegação
                </h5>
                <nav class="space-y-4">
                    <button wire:click="goToFolder(null)" 
                        class="flex items-center gap-3 w-full text-left p-3 rounded-2xl transition-all duration-300 {{ is_null($current_folder_id) ? 'bg-white dark:bg-zinc-800 shadow-lg shadow-primary-500/5 text-primary-600' : 'text-slate-500 hover:bg-white dark:hover:bg-zinc-800' }}">
                        <x-heroicon-o-home class="w-5 h-5"/>
                        <span class="text-xs font-black uppercase tracking-wider">Raiz</span>
                    </button>

                    @foreach($treeFolders as $tree)
                        @include('livewire.document-manager-tree-item', ['item' => $tree, 'level' => 0])
                    @endforeach
                </nav>
            </div>
        </aside>

        {{-- Área de Conteúdo --}}
        <main class="lg:col-span-9 space-y-4">
            {{-- Grid de Pastas Atuais --}}
            @if($folders->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($folders as $folder)
                        <div class="group relative bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-5 rounded-3xl shadow-sm hover:shadow-xl hover:scale-[1.03] transition-all duration-500 cursor-pointer"
                             wire:click="goToFolder({{ $folder->id }})">
                            <div class="flex items-start justify-between">
                                <x-heroicon-s-folder class="w-12 h-12 text-amber-400 group-hover:text-amber-500 transition-colors drop-shadow-sm"/>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                    <button wire:click.stop="mountAction('editFolder', { id: {{ $folder->id }} })" class="p-1.5 text-slate-300 hover:text-warning-500 transition-all">
                                        <x-heroicon-m-pencil class="w-4 h-4"/>
                                    </button>
                                    <button wire:click.stop="excluirPasta({{ $folder->id }})" wire:confirm="Excluir esta pasta e todos os seus vínculos?" class="p-1.5 text-slate-300 hover:text-rose-500 transition-all">
                                        <x-heroicon-m-trash class="w-4 h-4"/>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h6 class="text-sm font-black text-slate-900 dark:text-gray-100 truncate tracking-tight">{{ $folder->nome }}</h6>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $folder->subpastas_count ?? 0 }} Subpastas</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Lista de Arquivos --}}
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-slate-200 dark:border-zinc-800 overflow-hidden shadow-sm transition-all duration-500">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-100 dark:border-zinc-800">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Nome do Arquivo</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Tipo</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Tamanho</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($documentos as $doc)
                            <tr class="group hover:bg-slate-50/30 dark:hover:bg-zinc-800/30 transition-all duration-300">
                                <td class="px-8 py-5">
                                    <a href="{{ Storage::url($doc->caminho) }}" target="_blank" class="flex items-center gap-4 group/link">
                                        <div class="p-2.5 bg-slate-50 dark:bg-zinc-800 rounded-xl group-hover/link:bg-primary-50 dark:group-hover/link:bg-primary-900/30 transition-colors">
                                            @php
                                                $icon = match($doc->extensao) {
                                                    'pdf' => 'heroicon-m-document-text',
                                                    'docx', 'doc' => 'heroicon-m-document',
                                                    'xls', 'xlsx' => 'heroicon-m-table-cells',
                                                    'jpg', 'png', 'webp' => 'heroicon-m-photo',
                                                    default => 'heroicon-m-document-outline'
                                                };
                                            @endphp
                                            <x-dynamic-component :component="$icon" class="w-5 h-5 text-slate-400 group-hover/link:text-primary-600 transition-colors"/>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 dark:text-gray-200 group-hover:text-primary-600 transition-colors truncate max-w-md">{{ $doc->nome_arquivo }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $doc->created_at->format('d/m/Y \à\s H:i') }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="inline-block px-3 py-1 bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-gray-300 rounded-full text-[9px] font-black uppercase tracking-widest">{{ $doc->extensao }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-xs font-bold text-slate-500">{{ number_format($doc->tamanho / 1024, 2) }} KB</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="mountAction('editDocument', { id: {{ $doc->id }} })" class="p-2 text-slate-300 hover:text-warning-600 hover:bg-warning-50 rounded-xl transition-all"><x-heroicon-m-pencil class="w-4 h-4"/></button>
                                        <a href="{{ $doc->url }}" download class="p-2 text-slate-300 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all"><x-heroicon-m-arrow-down-tray class="w-4 h-4"/></a>
                                        <button wire:click="excluirDocumento({{ $doc->id }})" wire:confirm="Excluir permanentemente este arquivo?" class="p-2 text-slate-300 hover:text-danger-600 hover:bg-danger-50 rounded-xl transition-all"><x-heroicon-m-trash class="w-4 h-4"/></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <x-heroicon-o-inbox class="w-12 h-12 text-slate-200 mx-auto mb-4"/>
                                    <p class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Nenhum arquivo nesta pasta</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    {{-- Modals do Filament --}}
    <x-filament-actions::modals />
</div>