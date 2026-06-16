<div>
    @if($task->pecaProcessual)
        <div class="bg-slate-50 dark:bg-zinc-800/50 rounded-xl p-6 border border-slate-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-zinc-200">
                    Detalhes da Produção
                </h3>
                <div class="flex items-center gap-2">
                    {{ $this->editarPecaAction }}
                    {{ $this->excluirPecaAction }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs font-medium text-slate-500 dark:text-zinc-400">Tipo de Peça</span>
                    <span class="block text-sm text-slate-900 dark:text-zinc-100 mt-1 font-medium">
                        {{ $task->pecaProcessual->tipo_peca->getLabel() }}
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-500 dark:text-zinc-400">Data de Produção</span>
                    <span class="block text-sm text-slate-900 dark:text-zinc-100 mt-1">
                        {{ $task->pecaProcessual->data_producao->format('d/m/Y') }}
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-500 dark:text-zinc-400">Autor</span>
                    <span class="block text-sm text-slate-900 dark:text-zinc-100 mt-1">
                        {{ $task->pecaProcessual->autor?->name ?? '—' }}
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-500 dark:text-zinc-400">Processo Vinculado</span>
                    <span class="block text-sm text-slate-900 dark:text-zinc-100 mt-1">
                        @if($task->pecaProcessual->processo)
                            <a href="{{ route('processos.show', $task->pecaProcessual->processo_id) }}" class="text-primary-600 hover:underline" target="_blank">
                                {{ $task->pecaProcessual->processo->numero_processo }}
                            </a>
                        @else
                            <span class="text-slate-400 italic">Sem processo vinculado</span>
                        @endif
                    </span>
                </div>
                
                @if($task->pecaProcessual->observacoes)
                <div class="col-span-2 mt-2">
                    <span class="block text-xs font-medium text-slate-500 dark:text-zinc-400">Observações</span>
                    <p class="text-sm text-slate-700 dark:text-zinc-300 mt-1 whitespace-pre-line">
                        {{ $task->pecaProcessual->observacoes }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    @else
        <div class="text-center py-8 bg-slate-50 dark:bg-zinc-800/50 rounded-xl border border-slate-200 dark:border-zinc-700 border-dashed">
            <div class="mx-auto w-12 h-12 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm mb-4">
                <x-heroicon-o-document-text class="w-6 h-6 text-slate-400 dark:text-zinc-500" />
            </div>
            <h3 class="text-sm font-semibold text-slate-800 dark:text-zinc-200 mb-2">Nenhuma peça registrada</h3>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mb-6 max-w-sm mx-auto">
                Registre uma peça processual associada a esta tarefa para acompanhar a produtividade da equipe.
            </p>
            {{ $this->registrarPecaAction }}
        </div>
    @endif

    <x-filament-actions::modals />
</div>
