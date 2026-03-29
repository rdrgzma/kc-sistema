<div class="flex flex-col h-full bg-slate-100 dark:bg-zinc-950 min-h-screen" x-data="{ draggingTask: null, overBucket: null }">
    
    @if(!$selectedPlannerId)
        {{-- INDEX VIEW: Grid of Planners --}}
        <div class="p-8 space-y-10 max-w-7xl mx-auto w-full">
            <div class="flex justify-between items-center bg-white dark:bg-zinc-900 p-8 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Quadros de Planejamento</h1>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 mt-2 font-medium italic">Gerencie seus fluxos de trabalho com clareza e precisão.</p>
                </div>
                <button wire:click="mountAction('createPlanner')" class="px-8 py-3.5 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 transition-all text-sm font-bold shadow-lg shadow-primary-500/20 flex items-center gap-3 active:scale-95 border border-primary-500">
                    <x-flux::icon.plus class="w-5 h-5" /> Novo Quadro
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($plannersData as $planner)
                    <div 
                        wire:click="selectPlanner({{ $planner->id }})"
                        class="group bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-2xl hover:shadow-primary-500/10 transition-all cursor-pointer relative overflow-hidden flex flex-col min-h-[18rem]"
                    >
                        {{-- Decorative background element --}}
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-500/5 rounded-full blur-2xl group-hover:bg-primary-500/10 transition-all"></div>

                        <div class="flex flex-col h-full space-y-6 relative z-10">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800 rounded-2xl border border-slate-200 dark:border-zinc-700 flex items-center justify-center text-primary-600 dark:text-primary-400 shadow-inner group-hover:scale-110 group-hover:bg-primary-50 transition-all">
                                <x-flux::icon.layout-grid class="w-8 h-8" />
                            </div>
                            
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors leading-tight">{{ $planner->name }}</h3>
                                <p class="text-sm text-slate-600 dark:text-zinc-400 mt-3 line-clamp-3 font-medium leading-relaxed italic">
                                    {{ $planner->description ?: 'Organize suas tarefas e prazos de forma centralizada.' }}
                                </p>
                            </div>

                            <div class="pt-6 border-t border-slate-200 dark:border-zinc-800 flex items-center justify-between mt-auto">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-900 dark:bg-zinc-700 flex items-center justify-center text-xs font-black text-white shadow-md border border-slate-700">
                                        {{ strtoupper(substr($planner->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold leading-none">Criado por</span>
                                        <span class="text-xs text-slate-800 dark:text-zinc-300 font-black mt-1">{{ $planner->user->name ?? 'Sistema' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-full border border-primary-100/50">
                                    <span class="text-[11px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-tighter">
                                        {{ $planner->tasks_count }} Atividades
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-24 bg-white dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-zinc-800 shadow-inner">
                        <div class="w-24 h-24 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-8 animate-pulse border border-slate-200">
                            <x-flux::icon.layout-grid class="w-12 h-12 text-slate-300 dark:text-zinc-600" />
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">Seu espaço está vazio</h3>
                        <p class="mt-3 text-slate-500 dark:text-zinc-500 text-center max-w-sm font-medium">Crie seu primeiro quadro para começar a orquestrar suas tarefas com excelência.</p>
                        <button wire:click="mountAction('createPlanner')" class="mt-10 px-8 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-2xl font-black hover:scale-105 transition-transform shadow-xl shadow-slate-900/10 active:scale-95">
                            Começar agora
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- DETAIL VIEW: The Board --}}
        @php $planner = $plannersData; @endphp
        <div class="flex flex-col h-screen overflow-hidden">
            {{-- Board Header --}}
            <div class="px-8 py-6 bg-white dark:bg-zinc-900 border-b border-slate-300 dark:border-zinc-800 flex justify-between items-center shrink-0 shadow-sm relative z-20">
                <div class="flex items-center gap-10">
                    <button wire:click="backToIndex" class="group flex items-center gap-4 text-slate-500 hover:text-primary-600 dark:text-zinc-400 dark:hover:text-primary-400 transition-colors">
                        <div class="p-3 bg-slate-50 dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 rounded-2xl group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20 group-hover:border-primary-200 transition-all shadow-sm">
                            <x-flux::icon.arrow-left class="w-5 h-5" />
                        </div>
                        <span class="text-xs font-black uppercase tracking-[0.25em]">Visão Geral</span>
                    </button>
                    
                    <div class="h-10 w-px bg-slate-300 dark:bg-zinc-800"></div>

                    <div>
                        <div class="flex items-center gap-5">
                            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $planner->name }}</h2>
                            @if($planner->plannable)
                                <div class="flex items-center px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-900/30 rounded-full">
                                    <span class="text-[10px] uppercase tracking-widest font-black text-indigo-700 dark:text-indigo-400">
                                        {{ class_basename($planner->plannable_type) }} #{{ $planner->plannable_id }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        @if($planner->description)
                            <p class="text-xs text-slate-500 dark:text-zinc-500 font-black mt-2 uppercase tracking-[0.15em] opacity-80">{{ $planner->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-5">
                    <button wire:click="mountAction('createBucket', { planner_id: {{ $planner->id }} })" class="p-3.5 bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 rounded-2xl hover:bg-primary-50 hover:text-primary-600 transition-all border border-slate-300 dark:border-zinc-700 hover:border-primary-400 group shadow-sm" title="Nova Coluna">
                        <x-flux::icon.plus-circle class="w-6 h-6 group-hover:rotate-90 transition-transform" />
                    </button>
                    <button wire:click="mountAction('createTask', { bucket_id: {{ $planner->buckets->first()?->id }} })" class="px-8 py-3.5 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 transition-all text-sm font-black shadow-lg shadow-primary-500/20 active:scale-95 flex items-center gap-3 border border-primary-500">
                        <x-flux::icon.plus class="w-5 h-5" /> Tarefa
                    </button>
                </div>
            </div>

            {{-- Horizontal Scrolling Area --}}
            <div class="flex-1 overflow-x-auto overflow-y-hidden bg-slate-100 dark:bg-zinc-950 p-8 custom-scrollbar-h">
                <div class="flex gap-10 h-full items-start">
                    
                    @foreach($planner->buckets as $bucket)
                        <div class="flex flex-col w-[24rem] min-w-[24rem] h-full bg-white/40 dark:bg-zinc-900/50 rounded-[2.5rem] border border-slate-300 dark:border-zinc-800/80 transition-all overflow-hidden shadow-inner"
                             :class="{ 'ring-4 ring-primary-500/30 bg-primary-50/50 dark:bg-primary-900/10 border-primary-300': overBucket === {{ $bucket->id }} }"
                             @dragover.prevent="overBucket = {{ $bucket->id }}"
                             @dragleave.prevent="overBucket = null"
                             @drop.prevent="$wire.updateTaskBucket(draggingTask, {{ $bucket->id }}); overBucket = null">
                            
                            {{-- Bucket Header --}}
                            <div class="flex items-center justify-between p-7 shrink-0 bg-white dark:bg-zinc-900/50 border-b border-slate-300 dark:border-zinc-800 backdrop-blur-sm">
                                <h3 
                                    class="font-black text-slate-900 dark:text-white uppercase text-[12px] tracking-[0.2em] flex items-center cursor-pointer hover:text-primary-600 transition-colors group"
                                    wire:click="mountAction('editBucket', { bucket_id: {{ $bucket->id }} })"
                                >
                                    <span class="w-6 h-2 rounded-full mr-5 shadow-sm group-hover:scale-x-150 transition-transform origin-left" style="background-color: {{ $bucket->color ?: '#cbd5e1' }}"></span>
                                    {{ $bucket->name }}
                                </h3>
                                <div class="px-3 py-1 bg-slate-100 dark:bg-zinc-800 text-[11px] rounded-xl text-slate-800 dark:text-zinc-400 border border-slate-300 dark:border-zinc-700 font-black shadow-sm">
                                    {{ $bucket->tasks->count() }}
                                </div>
                            </div>

                            {{-- Tasks Container (Internal Scroll) --}}
                            <div class="flex-1 overflow-y-auto px-6 py-8 space-y-5 custom-scrollbar bg-slate-200/20">
                                @foreach($bucket->tasks as $task)
                                    <div 
                                        draggable="true"
                                        @dragstart.self="draggingTask = {{ $task->id }}; event.dataTransfer.effectAllowed = 'move';"
                                        @dragend.self="draggingTask = null"
                                        :class="{ 'opacity-50 scale-95 ring-4 ring-primary-500/50 shadow-2xl border-primary-400': draggingTask === {{ $task->id }} }"
                                        class="group bg-white dark:bg-zinc-800 p-6 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 hover:border-primary-400 dark:hover:border-primary-500 transition-all cursor-grab active:cursor-grabbing relative overflow-hidden hover:shadow-xl hover:shadow-slate-300/40 dark:hover:shadow-none"
                                        wire:click="mountAction('editTask', { task_id: {{ $task->id }} })"
                                    >
                                        {{-- Visual Urgency Indicator --}}
                                        @php $colorArr = $task->urgency->getColor(); @endphp
                                        @php $colorClass = is_array($colorArr) ? ($colorArr[500] ?? 'slate') : $colorArr; @endphp
                                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-{{ $colorClass }}-500 opacity-25 group-hover:opacity-100 transition-opacity"></div>

                                        <div class="flex flex-col gap-5">
                                            <h4 class="text-md font-black text-slate-900 dark:text-gray-100 leading-[1.5] group-hover:text-primary-600 transition-colors tracking-tight">
                                                {{ $task->title }}
                                            </h4>
                                            
                                            <div class="flex items-center justify-between pt-5 border-t border-slate-200 dark:border-zinc-700/50">
                                                <div class="flex items-center gap-2.5 {{ $task->due_date && $task->due_date->isPast() ? 'text-red-600 font-black' : 'text-slate-500 dark:text-zinc-500' }}">
                                                    <x-flux::icon.calendar class="w-4.5 h-4.5" />
                                                    <span class="text-[11px] font-black uppercase tracking-[0.15em]">
                                                        {{ $task->due_date ? $task->due_date->format('d/m') : 'Sem Prazo' }}
                                                    </span>
                                                </div>

                                                @if($task->assignee)
                                                    <div class="w-9 h-9 rounded-[1rem] bg-slate-900  text-white dark:bg-zinc-100 dark:text-slate-900 border border-slate-800 dark:border-zinc-800 flex items-center justify-center font-black text-[11px] group-hover:bg-primary-600 group-hover:border-primary-600 transition-all shadow-md" title="{{ $task->assignee->name }}">
                                                        {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Add Task Inline Button --}}
                                <button 
                                    wire:click="mountAction('createTask', { bucket_id: {{ $bucket->id }} })"
                                    class="w-full py-5 rounded-[2rem] border-2 border-dashed border-slate-300 dark:border-zinc-800 hover:border-primary-400 dark:hover:border-primary-700 hover:bg-white dark:hover:bg-zinc-800/80 text-slate-500 hover:text-primary-600 transition-all group flex items-center justify-center gap-4 active:scale-95 bg-white/20"
                                >
                                    <x-flux::icon.plus class="w-6 h-6 transition-transform group-hover:rotate-90" />
                                    <span class="text-[12px] font-black uppercase tracking-[0.25em] group-hover:text-primary-600">Adicionar Tarefa</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Empty State / Add Column spacer --}}
                    <div class="w-32 shrink-0 h-full flex items-center justify-center">
                         <div class="w-2 h-16 bg-slate-300 dark:bg-zinc-800 rounded-full opacity-30"></div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }

    .custom-scrollbar-h::-webkit-scrollbar { height: 12px; }
    .custom-scrollbar-h::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar-h::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 3px solid transparent; background-clip: content-box; }
    .dark .custom-scrollbar-h::-webkit-scrollbar-thumb { background: #27272a; }
</style>