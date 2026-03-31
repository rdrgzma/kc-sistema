<?php
use App\Models\Pessoa;
use App\Models\Processo;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $query = '';

    #[Computed]
    public function results()
    {
        if (strlen($this->query) < 2) {
            return [
                'processos' => collect(),
                'pessoas' => collect(),
            ];
        }

        $escritorioId = auth()->user()->escritorio_id;

        $processos = Processo::query()
            ->where('escritorio_id', $escritorioId)
            ->where(function ($q) {
                $q->where('numero_processo', 'like', '%' . $this->query . '%');

                $strippedQuery = preg_replace('/[^0-9]/', '', $this->query);
                if (strlen($strippedQuery) >= 3) {
                    $q->orWhereRaw("REPLACE(REPLACE(numero_processo, '.', ''), '-', '') LIKE ?", ['%' . $strippedQuery . '%']);
                }
            })
            ->with(['pessoa:id,nome_razao'])
            ->latest()
            ->take(5)
            ->get();

        $pessoas = Pessoa::query()
            ->where('escritorio_id', $escritorioId)
            ->where(function ($q) {
                $q->where('nome_razao', 'like', '%' . $this->query . '%')
                    ->orWhere('cpf_cnpj', 'like', '%' . $this->query . '%');
            })
            ->latest()
            ->take(5)
            ->get();

        return [
            'processos' => $processos,
            'pessoas' => $pessoas,
        ];
    }

    public function clear(): void
    {
        $this->query = '';
    }
}
?>

<div class="relative w-80 group" x-data="{ searchShow: false }" @click.away="searchShow = false">
    {{-- Input de Busca --}}
    <div class="relative flex items-center">
        <div 
            class="absolute inset-x-0 bottom-0 h-0.5 bg-amber-500 scale-x-0 transition-transform origin-left rounded-full shadow-[0_0_10px_rgba(245,158,11,0.3)]"
            x-bind:class="searchShow ? 'scale-x-100' : 'group-focus-within:scale-x-100'">
        </div>
        
        <x-heroicon-o-magnifying-glass 
            class="w-5 h-5 absolute left-0 text-slate-400 dark:text-zinc-500 transition-colors"
            x-bind:class="searchShow ? 'text-amber-500' : 'group-focus-within:text-amber-500'" />
            
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            @focus="searchShow = true"
            placeholder="Buscar processos, pessoas..."
            class="w-full bg-transparent dark:bg-transparent border-none px-8 py-3 text-sm font-bold text-slate-700 dark:text-zinc-300 placeholder-slate-400 focus:ring-0 transition-all outline-none italic tracking-wide"
        >
        
        <div wire:loading wire:target="query" class="absolute right-0 bottom-3">
            <x-flux::icon.loading class="w-4 h-4 text-amber-500 animate-spin" />
        </div>

        <button 
            x-show="$wire.query.length > 0" 
            @click="$wire.clear(); searchShow = false" 
            class="absolute right-0 text-slate-400 hover:text-red-500 transition-colors"
        >
            <x-heroicon-m-x-mark class="w-4 h-4" />
        </button>
    </div>

    {{-- Dropdown de Resultados --}}
    <div 
        x-show="searchShow && $wire.query.length >= 2"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="absolute left-0 right-0 mt-3 bg-white dark:bg-zinc-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-zinc-800 z-[100] overflow-hidden max-h-[32rem] overflow-y-auto p-2"
    >
        @php $results = $this->results(); @endphp

        @if($results['processos']->isEmpty() && $results['pessoas']->isEmpty())
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-zinc-700">
                    <x-heroicon-o-magnifying-glass class="w-8 h-8 text-slate-300 dark:text-zinc-600" />
                </div>
                <p class="text-sm font-black text-slate-900 dark:text-zinc-50 tracking-tight">Nenhum resultado encontrado</p>
                <p class="text-xs font-bold text-slate-400 dark:text-zinc-500 mt-1 uppercase tracking-widest">Tente com outros termos</p>
            </div>
        @else
            {{-- Resultados: Processos --}}
            @if($results['processos']->isNotEmpty())
                <div class="p-2">
                    <h3 class="px-4 pt-2 pb-1 text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-[0.2em]">Processos</h3>
                    <div class="flex flex-col gap-1 mt-1">
                        @foreach($results['processos'] as $processo)
                            <a 
                                href="{{ route('processos.show', $processo) }}" 
                                wire:navigate
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-2xl transition-all group border border-transparent hover:border-slate-100 dark:hover:border-zinc-700"
                            >
                                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center border border-amber-100 dark:border-amber-900/30 group-hover:scale-105 transition-transform">
                                    <x-heroicon-o-scale class="w-5 h-5 text-amber-600 dark:text-amber-500" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 dark:text-zinc-50 leading-none group-hover:text-amber-600 transition-colors">{{ $processo->numero_processo }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 mt-1.5 uppercase tracking-widest">{{ $processo->pessoa?->nome_razao ?? 'Sem Cliente' }}</span>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 ml-auto text-slate-300 dark:text-zinc-700 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Resultados: Pessoas --}}
            @if($results['pessoas']->isNotEmpty())
                <div class="p-2 {{ $results['processos']->isNotEmpty() ? 'border-t border-slate-100 dark:border-zinc-800 mt-2' : '' }}">
                    <h3 class="px-4 pt-2 pb-1 text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-[0.2em]">Pessoas & Clientes</h3>
                    <div class="flex flex-col gap-1 mt-1">
                        @foreach($results['pessoas'] as $pessoa)
                            <a 
                                href="{{ route('pessoas.show', $pessoa) }}"
                                wire:navigate 
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-2xl transition-all group border border-transparent hover:border-slate-100 dark:hover:border-zinc-700"
                            >
                                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center border border-blue-100 dark:border-blue-900/30 group-hover:scale-105 transition-transform">
                                    <x-heroicon-o-user class="w-5 h-5 text-blue-600 dark:text-blue-500" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 dark:text-zinc-50 leading-none group-hover:text-blue-600 transition-colors">{{ $pessoa->nome_razao }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 mt-1.5 uppercase tracking-widest">{{ $pessoa->cpf_cnpj }}</span>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 ml-auto text-slate-300 dark:text-zinc-700 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
