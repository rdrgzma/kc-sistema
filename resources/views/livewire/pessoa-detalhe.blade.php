<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Coluna lateral --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Contato --}}
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-8">
                <h4 class="text-[10px] font-black text-slate-400 dark:text-gray-200 uppercase tracking-[0.2em] mb-6 border-b border-slate-100 dark:border-zinc-800 pb-3">Informações de Contato</h4>
                <div class="space-y-6 text-sm">
                    <div>
                        <p class="text-slate-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Documento</p>
                        <p class="font-black text-slate-900 dark:text-gray-200">{{ $pessoa->cpf_cnpj }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">E-mail Corporativo</p>
                        <p class="font-black text-primary-600 dark:text-primary-400 italic underline underline-offset-4 decoration-primary-200">{{ $pessoa->email ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Telefone / WhatsApp</p>
                        <p class="font-black text-slate-900 dark:text-gray-200">{{ $pessoa->telefone ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>

            {{-- Endereço --}}
            <div class="bg-slate-200/50 dark:bg-zinc-900/50 rounded-[1.5rem] border border-slate-300 dark:border-zinc-800 p-8 shadow-inner">
                <h4 class="text-[10px] font-black text-slate-500 dark:text-gray-200 uppercase tracking-[0.25em] mb-4">Endereço Registrado</h4>
                <p class="text-xs text-slate-700 dark:text-gray-500 leading-relaxed font-bold italic">
                    {{ $pessoa->logradouro }}, {{ $pessoa->numero }} {{ $pessoa->complemento }}<br>
                    {{ $pessoa->bairro }} - {{ $pessoa->cidade }}/{{ $pessoa->estado }}<br>
                    CEP: {{ $pessoa->cep }}
                </p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-10">
                <div class="flex items-center gap-4 mb-8 border-b border-slate-100 dark:border-zinc-800 pb-5">
                    <x-heroicon-o-folder-open class="w-7 h-7 text-primary-600 dark:text-primary-400"/>
                    <h4 class="text-[12px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.3em]">GED: DOCUMENTOS E CONTRATOS</h4>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:document-manager :model="$pessoa" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-10">
                <div class="flex items-center gap-4 mb-8 border-b border-slate-100 dark:border-zinc-800 pb-5">
                    <x-heroicon-o-banknotes class="w-7 h-7 text-emerald-600 dark:text-emerald-400"/>
                    <h4 class="text-[12px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.3em]">LANÇAMENTOS FINANCEIROS</h4>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:financeiro-manager :model="$pessoa" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-slate-300 dark:border-zinc-800 p-10">
                <div class="flex items-center gap-4 mb-8 border-b border-slate-100 dark:border-zinc-800 pb-5">
                    <x-heroicon-o-chat-bubble-left-right class="w-7 h-7 text-indigo-600 dark:text-indigo-400"/>
                    <h4 class="text-[12px] font-black text-slate-900 dark:text-gray-200 uppercase tracking-[0.3em]">HISTÓRICO DE ATENDIMENTOS</h4>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-100 dark:border-zinc-800">
                    <livewire:interacao-manager :model="$pessoa" />
                </div>
            </div>
        </div>
    </div>
</div>