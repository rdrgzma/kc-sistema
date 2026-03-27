<?php

use App\Models\Pessoa;
use function Livewire\Volt\{state, mount, layout};

// Define o layout global
layout('layouts.app');

// Define o estado do componente
state(['pessoa']);

// Carrega os dados na montagem, incluindo as relações
mount(function (Pessoa $pessoa) {
    $this->pessoa = $pessoa->load('documentos');
});

?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-end border-b pb-6">
        <div>
            <nav class="flex mb-2">
                <ol class="flex items-center space-x-2 text-xs text-gray-400">
                    <li><a href="{{ route('pessoas.index') }}" wire:navigate class="hover:text-blue-600">Pessoas</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li class="font-bold text-gray-600">{{ $pessoa->nome_razao }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900">{{ $pessoa->nome_razao }}</h1>
            <p class="text-sm text-gray-500 mt-1 italic">{{ $pessoa->tipo === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Informações de Contato</h4>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Documento (CPF/CNPJ)</p>
                        <p class="font-bold text-gray-800">{{ $pessoa->cpf_cnpj }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">E-mail</p>
                        <p class="font-medium text-blue-600">{{ $pessoa->email ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Telefone</p>
                        <p class="font-medium text-gray-800">{{ $pessoa->telefone ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 text-xs">Endereço</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ $pessoa->logradouro }}, {{ $pessoa->numero }} {{ $pessoa->complemento }}<br>
                    {{ $pessoa->bairro }} - {{ $pessoa->cidade }}/{{ $pessoa->estado }}<br>
                    CEP: {{ $pessoa->cep }}
                </p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-4 border-b pb-2">
                    <x-heroicon-o-folder-open class="w-5 h-5 text-blue-500"/>
                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">GED: Documentos e Contratos</h4>
                </div>
                
                <livewire:document-manager :model="$pessoa" />
                <livewire:financeiro-manager :model="$pessoa" />
            </div>
        </div>
    </div>
</div>