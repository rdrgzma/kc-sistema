<?php

use App\Models\Processo;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new class extends Component 
{
    public Processo $processo;

    public function mount(Processo $processo)
    {
        // Carregamos as relações necessárias
        $this->processo = $processo->load(['pessoa', 'timelineEvents.user']);
    }


}; // <-- O fechamento da classe DEVE estar aqui, após os métodos.
?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-end border-b pb-6">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs text-gray-400">
                    <li><a href="{{ route('processos.index') }}" wire:navigate class="hover:text-blue-600">Processos</a></li>
                    <li><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                    <li class="font-bold text-gray-600 dark:text-gray-200">{{ $processo->numero_processo }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-200">{{ $processo->pessoa->nome_razao }}</h1>
            <p class="text-sm text-gray-500 mt-1 dark:text-gray-200 ">Nº: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-blue-700 dark:text-gray-200 dark:bg-gray-700">{{ $processo->numero_processo }}</span></p>
        </div>
        
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:text-gray-200 dark:bg-gray-700">
            Ativo
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 dark:bg-zinc-900 dark:border-zinc-800">
                <livewire:timeline-feed :processo="$processo" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 dark:bg-zinc-900 dark:border-zinc-800" >
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2 dark:text-gray-200 dark:border-gray-700">Resumo</h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase dark:text-gray-200 dark:border-gray-700">Economia Gerada</p>
                        <p class="text-xl font-bold text-emerald-600 dark:text-gray-200 dark:border-gray-700">R$ {{ number_format($processo->economia_gerada, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase dark:text-gray-200 dark:border-gray-700">Perda Estimada</p>
                        <p class="t ext-xl font-bold text-rose-600 dark:text-gray-200 dark:border-gray-700">R$ {{ number_format($processo->perda_estimada, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 dark:bg-zinc-900 dark:border-zinc-800">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2 dark:text-gray-200 dark:border-gray-700">Documentos</h4>
                <livewire:document-manager :model="$processo" />
            </div>

            <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-6 text-sm space-y-2 dark:bg-zinc-900 dark:border-zinc-800">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2 dark:text-gray-200 dark:border-gray-700">Cliente</h4>
                <p><span class="text-gray-500 dark:text-gray-200 dark:border-gray-700">Nome:</span> {{ $processo->pessoa->nome_razao }}</p>
                <p><span class="text-gray-500 dark:text-gray-200 dark:border-gray-700">Doc:</span> {{ $processo->pessoa->cpf_cnpj }}</p>
                <livewire:financeiro-manager :model="$processo" />
            </div>
        </div>
    </div>
</div>