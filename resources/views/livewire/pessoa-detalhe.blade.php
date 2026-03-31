<div class="max-w-full mx-auto py-8 px-8 lg:px-12 transition-all duration-500">
    {{-- Header --}}
    <div class="mb-12 flex justify-between items-end border-b border-slate-300 dark:border-zinc-800 pb-10">
        <div>
            <nav class="flex mb-4">
                <ol class="flex items-center space-x-3 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">
                    <li><a href="{{ route('pessoas.index') }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Pessoas</a></li>
                    <li><span class="text-slate-300 dark:text-zinc-600">/</span></li>
                    <li class="text-slate-900 dark:text-gray-200">{{ $pessoa->nome_razao }}</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-slate-900 dark:text-gray-200 tracking-tight">{{ $pessoa->nome_razao }}</h1>
            <p class="text-[11px] font-black text-slate-500 dark:text-gray-500 mt-3 uppercase tracking-[0.2em] italic">{{ $pessoa->tipo === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
        </div>
    </div>

    {{-- Informações do Cliente Topo --}}
    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Contato --}}
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8 flex items-center gap-8">
            <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-2xl border border-slate-100 dark:border-zinc-700/50">
                <x-heroicon-o-identification class="w-8 h-8 text-primary-600 dark:text-primary-400"/>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 flex-1">
                <div>
                    <p class="text-slate-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Documento</p>
                    <p class="font-black text-slate-900 dark:text-gray-200 text-sm">{{ $pessoa->cpf_cnpj }}</p>
                </div>
                <div>
                    <p class="text-slate-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">E-mail</p>
                    <p class="font-black text-primary-600 dark:text-primary-400 text-sm truncate italic underline underline-offset-4 decoration-primary-200">{{ $pessoa->email ?? 'Não informado' }}</p>
                </div>
            </div>
        </div>

        {{-- Endereço --}}
        <div class="bg-slate-50/50 dark:bg-zinc-900/50 rounded-[2rem] border border-slate-300 dark:border-zinc-800 p-8 flex items-center gap-8 shadow-inner">
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-2xl border border-slate-100 dark:border-zinc-700/50">
                <x-heroicon-o-map-pin class="w-8 h-8 text-slate-500 dark:text-gray-400"/>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 dark:text-gray-200 uppercase tracking-[0.25em] mb-1 italic">Localização</p>
                <p class="text-[11px] text-slate-700 dark:text-gray-400 leading-relaxed font-bold">
                    {{ $pessoa->logradouro }}, {{ $pessoa->numero }} • {{ $pessoa->bairro }} • {{ $pessoa->cidade }}/{{ $pessoa->estado }}
                </p>
            </div>
        </div>
    </div>

    {{-- Content Cluster Full-Width --}}
    <div 
        class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 transition-all duration-300" 
        x-data="{ tab: 'documentos' }"
        :class="['financeiro', 'documentos'].includes(tab) ? 'p-4 lg:p-6' : 'p-6 lg:p-10'"
    >
        {{-- Cluster Navigation --}}
        <div class="flex flex-wrap p-1.5 bg-slate-100 dark:bg-zinc-800 rounded-3xl mb-10 w-fit gap-1">
            <button 
                @click="tab = 'documentos'" 
                :class="tab === 'documentos' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-folder-open class="w-5 h-5"/>
                Documentos
            </button>
            <button 
                @click="tab = 'financeiro'" 
                :class="tab === 'financeiro' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-currency-dollar class="w-5 h-5"/>
                Financeiro
            </button>
            <button 
                @click="tab = 'atendimentos'" 
                :class="tab === 'atendimentos' ? 'bg-white dark:bg-zinc-700 shadow-lg text-primary-600 dark:text-primary-400 scale-[1.02]' : 'text-slate-500 dark:text-gray-400 hover:text-slate-700'"
                class="flex items-center gap-3 px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-400 scale-100 hover:scale-[1.01]"
            >
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5"/>
                Atendimentos
            </button>
        </div>

        {{-- Tab Panels --}}
        <div x-show="tab === 'documentos'" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:document-manager :model="$pessoa" />
            </div>
        </div>

        <div x-show="tab === 'financeiro'" x-cloak x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:financeiro.financeiro-manager :model="$pessoa" />
            </div>
        </div>

        <div x-show="tab === 'atendimentos'" x-cloak x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="overflow-hidden bg-slate-50/30 dark:bg-zinc-900/30">
                <livewire:interacao-manager :model="$pessoa" />
            </div>
        </div>
    </div>
</div>