<div class="space-y-6">
    {{-- Formulário de registro --}}
    <div class="bg-indigo-50/30 dark:bg-zinc-900 p-5 rounded-xl border border-indigo-100 dark:border-zinc-800 shadow-sm">
        <h5 class="text-[10px] font-bold uppercase text-indigo-500 dark:text-gray-200 mb-3 tracking-widest flex items-center gap-2">
            <x-heroicon-o-chat-bubble-left-right class="w-4 h-4"/>
            Registrar Novo Atendimento
        </h5>

        {{ $this->form }}

        <div class="mt-4 flex justify-end">
            <button wire:click="registrar" class="bg-indigo-600 dark:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold text-xs hover:bg-indigo-700 dark:hover:bg-indigo-600 transition shadow-md">
                Salvar Interação
            </button>
        </div>
    </div>

    {{-- Lista de interações --}}
    <div class="space-y-4">
        @forelse($interacoes as $interacao)
            <div class="flex gap-4 p-4 bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-xl shadow-sm relative">
                {{-- Ícone do tipo --}}
                <div class="flex-shrink-0 mt-1">
                    @if($interacao->tipo === 'whatsapp')
                        <div class="p-2 bg-green-100 dark:bg-zinc-800 text-green-600 dark:text-green-400 rounded-full">
                            <x-heroicon-s-chat-bubble-oval-left class="w-4 h-4"/>
                        </div>
                    @elseif($interacao->tipo === 'email')
                        <div class="p-2 bg-blue-100 dark:bg-zinc-800 text-blue-600 dark:text-blue-400 rounded-full">
                            <x-heroicon-s-envelope class="w-4 h-4"/>
                        </div>
                    @elseif($interacao->tipo === 'telefone')
                        <div class="p-2 bg-purple-100 dark:bg-zinc-800 text-purple-600 dark:text-purple-400 rounded-full">
                            <x-heroicon-s-phone class="w-4 h-4"/>
                        </div>
                    @else
                        <div class="p-2 bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-200 rounded-full">
                            <x-heroicon-s-users class="w-4 h-4"/>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ $interacao->assunto }}</h4>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $interacao->data_interacao->format('d/m/Y \à\s H:i') }} •
                                Por {{ $interacao->user->name ?? 'Sistema' }}
                            </p>
                        </div>

                        @php
                            $statusClass = match($interacao->status) {
                                'realizada' => 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-200',
                                'agendada'  => 'bg-amber-100 dark:bg-zinc-800 text-amber-700 dark:text-amber-400',
                                default     => 'bg-rose-100 dark:bg-zinc-800 text-rose-700 dark:text-rose-400',
                            };
                        @endphp
                        <span class="flex-shrink-0 text-[9px] uppercase font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $interacao->status }}
                        </span>
                    </div>

                    @if($interacao->descricao)
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-200 leading-relaxed bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg border border-gray-100 dark:border-zinc-800">
                            {{ $interacao->descricao }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-6 border-2 border-dashed border-gray-100 dark:border-zinc-800 rounded-xl">
                <p class="text-xs text-gray-400 dark:text-gray-500 italic">Nenhum registro de atendimento encontrado.</p>
            </div>
        @endforelse
    </div>
</div>