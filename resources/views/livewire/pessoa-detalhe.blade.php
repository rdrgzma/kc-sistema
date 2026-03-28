<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8 flex justify-between items-end border-b dark:border-zinc-800 pb-6">
        <div>
            <nav class="flex mb-2">
                <ol class="flex items-center space-x-2 text-xs text-gray-400 dark:text-gray-500">
                    <li><a href="{{ route('pessoas.index') }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400">Pessoas</a></li>
                    <li><span class="text-gray-300 dark:text-zinc-600">/</span></li>
                    <li class="font-bold text-gray-600 dark:text-gray-200">{{ $pessoa->nome_razao }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-200">{{ $pessoa->nome_razao }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1 italic">{{ $pessoa->tipo === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Coluna lateral --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Contato --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 p-6">
                <h4 class="text-xs font-bold text-gray-400 dark:text-gray-200 uppercase tracking-wider mb-4 border-b dark:border-zinc-800 pb-2">Informações de Contato</h4>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-500 text-xs">Documento (CPF/CNPJ)</p>
                        <p class="font-bold text-gray-800 dark:text-gray-200">{{ $pessoa->cpf_cnpj }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-500 text-xs">E-mail</p>
                        <p class="font-medium text-blue-600 dark:text-blue-400">{{ $pessoa->email ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-500 text-xs">Telefone</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $pessoa->telefone ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>

            {{-- Endereço --}}
            <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6">
                <h4 class="text-xs font-bold text-gray-400 dark:text-gray-200 uppercase tracking-wider mb-3">Endereço</h4>
                <p class="text-xs text-gray-600 dark:text-gray-500 leading-relaxed">
                    {{ $pessoa->logradouro }}, {{ $pessoa->numero }} {{ $pessoa->complemento }}<br>
                    {{ $pessoa->bairro }} - {{ $pessoa->cidade }}/{{ $pessoa->estado }}<br>
                    CEP: {{ $pessoa->cep }}
                </p>
            </div>
        </div>

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-2 mb-4 border-b dark:border-zinc-800 pb-2">
                    <x-heroicon-o-folder-open class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider">GED: Documentos e Contratos</h4>
                </div>

                <livewire:document-manager :model="$pessoa" />
  
            </div>
                        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-2 mb-4 border-b dark:border-zinc-800 pb-2">
                    <x-heroicon-o-banknotes class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Lançamentos Financeiros</h4>
                </div>

             
                <livewire:financeiro-manager :model="$pessoa" />
                
            </div>
                        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-2 mb-4 border-b dark:border-zinc-800 pb-2">
                    <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Atendimentos</h4>
                </div>

     
                <livewire:interacao-manager :model="$pessoa" />
            </div>
        </div>
    </div>
</div>