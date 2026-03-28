<?php

namespace App\Livewire;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Livewire\Component;

class FinanceiroManager extends Component implements HasForms
{
    use InteractsWithForms;

    public $model;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount($model): void
    {
        $this->model = $model;

        $this->form->fill([
            'tipo' => 'R',
            'data_vencimento' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
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
                        'D' => 'Despesa (Saída)',
                    ])
                    ->required(),

                DatePicker::make('data_vencimento')
                    ->label('Data de Vencimento')
                    ->required(),
            ]);
    }

    public function registrar(): void
    {
        $data = $this->form->getState();

        $this->model->lancamentosFinanceiros()->create([
            'descricao' => $data['descricao'],
            'valor' => $data['valor'],
            'tipo' => $data['tipo'],
            'data_vencimento' => $data['data_vencimento'],
            'status' => 'pendente',
            'user_id' => auth()->id(),
        ]);

        $this->form->fill([
            'tipo' => 'R',
            'data_vencimento' => now()->format('Y-m-d'),
            'descricao' => null,
            'valor' => null,
        ]);

        $this->dispatch('notify', message: 'Lançamento financeiro registrado!');
    }

    public function render(): View
    {
        return view('livewire.financeiro-manager');
    }
}
