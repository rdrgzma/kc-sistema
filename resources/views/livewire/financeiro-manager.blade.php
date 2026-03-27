<?php

use App\Models\LancamentoFinanceiro;
use Livewire\Volt\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

new class extends Component implements HasForms
{
    use InteractsWithForms;

    public $model;

    public function mount($model) {
        $this->model = $model;
    }

    /**
     * Interface v5 baseada em Schemas
     */
    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->placeholder('Ex: Honorários Contratuais'),
                
                TextInput::make('valor')
                    ->label('Valor (R$)')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),

                Select::make('tipo')
                    ->label('Categoria')
                    ->options([
                        'R' => 'Receita (Entrada)',
                        'D' => 'Despesa (Saída)'
                    ])
                    ->default('R')
                    ->required(),

                DatePicker::make('data_vencimento')
                    ->label('Data de Vencimento')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function registrar()
    {
        // No v5, pegamos o estado do schema
        $data = $this->schema->getState();

        $this->model->lancamentosFinanceiros()->create([
            'descricao'       => $data['descricao'],
            'valor'           => $data['valor'],
            'tipo'            => $data['tipo'],
            'data_vencimento' => $data['data_vencimento'],
            'status'          => 'pendente',
            'user_id'         => auth()->id() ?? 1,
        ]);

        // Limpa o formulário após salvar
        $this->schema->fill();
        $this->dispatch('notify', message: 'Lançamento financeiro registrado!');
    }
};
?>

<div class="space-y-6">
    <div class="bg-blue-50/30 p-5 rounded-xl border border-blue-100 shadow-sm">
        <h5 class="text-[10px] font-bold uppercase text-blue-500 mb-3 tracking-widest">Novo Lançamento</h5>
        {{ $this->schema }}
        <button wire:click="registrar" class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition shadow-md shadow-blue-200">
            Adicionar ao Extrato
        </button>
    </div>

    <div class="space-y-2">
        @forelse($this->model->lancamentosFinanceiros()->latest()->get() as $item)
            <div class="flex items-center justify-between p-3 bg-white border rounded-lg shadow-sm">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-700">{{ $item->descricao }}</span>
                    <span class="text-[10px] text-gray-400">Vence em: {{ \Carbon\Carbon::parse($item->data_vencimento)->format('d/m/Y') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black {{ $item->tipo === 'R' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $item->tipo === 'D' ? '-' : '' }} R$ {{ number_format($item->valor, 2, ',', '.') }}
                    </span>
                    <div class="text-[9px] uppercase font-bold text-gray-400">{{ $item->status }}</div>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-4">Nenhum movimento financeiro.</p>
        @endforelse
    </div>
</div>