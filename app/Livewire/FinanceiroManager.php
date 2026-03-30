<?php

namespace App\Livewire;

use App\Models\LancamentoFinanceiro;
use App\Models\User;
use App\Services\FinanceiroService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class FinanceiroManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions, InteractsWithForms, InteractsWithTable;

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

    public function table(Table $table): Table
    {
        return $table
            ->query($this->model->lancamentosFinanceiros()->getQuery())
            ->columns([
                TextColumn::make('descricao')->label('Descrição')->searchable(),
                TextColumn::make('tipo')->label('Tipo')->badge()->color(fn (string $state): string => match ($state) {
                    'R' => 'success', 'D' => 'danger', 'despesa' => 'danger', 'receita' => 'success', default => 'gray'
                }),
                TextColumn::make('valor')->label('Valor (R$)')->money('BRL'),
                TextColumn::make('data_vencimento')->label('Vencimento')->date('d/m/Y'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('descricao')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('valor')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                        DatePicker::make('data_vencimento')
                            ->required(),
                        DatePicker::make('data_pagamento'),
                        Select::make('status')
                            ->options([
                                'pendente' => 'Pendente',
                                'pago' => 'Pago',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required(),
                    ]),
                Action::make('ratear_honorarios')
                    ->label('Ratear')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn (LancamentoFinanceiro $record) => $record->tipo === 'R' && auth()->user()->hasAnyRole(['Administrador', 'Sócio']))
                    ->form([
                        Select::make('tipo_rateio')
                            ->label('Tipo de Rateio')
                            ->options([
                                'exito' => 'Por Êxito (%)',
                                'horas' => 'Por Horas Trabalhadas',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('percentual_causa')
                            ->label('Percentual Total da Causa (%)')
                            ->numeric()
                            ->required()
                            ->visible(fn (Get $get) => $get('tipo_rateio') === 'exito'),

                        Select::make('advogados')
                            ->label('Advogados Rateados')
                            ->multiple()
                            ->options(User::pluck('name', 'id'))
                            ->required()
                            ->visible(fn (Get $get) => $get('tipo_rateio') === 'exito'),

                        Repeater::make('horas_trabalhadas')
                            ->label('Horas por Advogado')
                            ->components([
                                Select::make('user_id')
                                    ->label('Advogado')
                                    ->options(User::pluck('name', 'id'))
                                    ->required(),
                                TextInput::make('horas')
                                    ->label('Horas Trabalhadas')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->visible(fn (Get $get) => $get('tipo_rateio') === 'horas'),
                    ])
                    ->action(function (array $data, LancamentoFinanceiro $record, FinanceiroService $service) {
                        if ($data['tipo_rateio'] === 'exito') {
                            $service->calcularRateioExito($record, $data['advogados'], (float) $data['percentual_causa']);
                        } elseif ($data['tipo_rateio'] === 'horas') {
                            $horasPorAdvogado = [];
                            foreach ($data['horas_trabalhadas'] as $item) {
                                $horasPorAdvogado[$item['user_id']] = (float) $item['horas'];
                            }
                            $service->calcularRateioHoras($record, $horasPorAdvogado);
                        }

                        Notification::make()->success()->title('Rateio processado com sucesso!')->send();
                    }),
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
