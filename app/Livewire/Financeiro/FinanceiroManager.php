<?php

namespace App\Livewire\Financeiro;

use App\Models\LancamentoFinanceiro;
use App\Models\Pessoa;
use App\Models\Processo;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;

class FinanceiroManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public $model = null;

    public function mount($model = null)
    {
        $this->model = $model;
    }

    public function table(Table $table): Table
    {
        $query = LancamentoFinanceiro::query()->estratificado();

        if ($this->model) {
            $query->where('lancamentable_id', $this->model->id)
                ->where('lancamentable_type', get_class($this->model));
        }

        return $table
            ->query($query->with(['categoria', 'lancamentable'])->latest())
            ->columns([
                TextColumn::make('lancamentable_type')
                    ->label('Origem')
                    ->visible(fn () => ! $this->model) // Só mostra no dashboard global
                    ->formatStateUsing(function ($state, LancamentoFinanceiro $record) {
                        if (! $record->lancamentable) {
                            return '-';
                        }

                        $icon = match ($state) {
                            Pessoa::class => '👤',
                            Processo::class => '⚖️',
                            default => '📝'
                        };

                        $name = match ($state) {
                            Pessoa::class => $record->lancamentable->nome_razao,
                            Processo::class => 'Proc. '.$record->lancamentable->numero_processo,
                            default => 'Lançamento Manual'
                        };

                        return "{$icon} {$name}";
                    })
                    ->url(function (LancamentoFinanceiro $record) {
                        if (! $record->lancamentable) {
                            return null;
                        }

                        return match ($record->lancamentable_type) {
                            Pessoa::class => route('pessoas.show', $record->lancamentable_id),
                            Processo::class => route('processos.show', $record->lancamentable_id),
                            default => null
                        };
                    })
                    ->color('primary')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('data_vencimento')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receita' => 'success',
                        'despesa' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->grow()
                    ->searchable(),

                TextColumn::make('categoria.nome')
                    ->label('Categoria')
                    ->placeholder('Sem categoria')
                    ->searchable(),

                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pago' => 'success',
                        'pendente' => 'warning',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('data_pagamento')
                    ->label('Data Pagto')
                    ->date('d/m/Y')
                    ->placeholder('-'),

                TextColumn::make('documentos_count')
                    ->label('')
                    ->counts('documentos')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '📎' : '')
                    ->tooltip(fn ($state) => $state > 0 ? "{$state} anexos" : 'Sem anexos'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'receita' => 'Receita',
                        'despesa' => 'Despesa',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'cancelado' => 'Cancelado',
                    ]),
                SelectFilter::make('categoria_financeira_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome'),
                Filter::make('data_vencimento')
                    ->form([
                        DatePicker::make('desde'),
                        DatePicker::make('ate'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn ($query, $date) => $query->whereDate('data_vencimento', '>=', $date))
                            ->when($data['ate'], fn ($query, $date) => $query->whereDate('data_vencimento', '<=', $date));
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Lançamento')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema())
                    ->modalWidth('2xl'),
                Action::make('export_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(fn () => $this->exportToPdf()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema())
                    ->modalWidth('2xl'),
                Action::make('marcar_pago')
                    ->label('Marcar Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (LancamentoFinanceiro $record) => $record->status === 'pago')
                    ->action(fn (LancamentoFinanceiro $record) => $record->update([
                        'status' => 'pago',
                        'data_pagamento' => now(),
                    ])),
                Action::make('anexos')
                    ->label('Anexos')
                    ->icon('heroicon-o-paper-clip')
                    ->color('info')
                    ->form([
                        FileUpload::make('arquivos')
                            ->label('Anexar Recibos/Boletos/Notas')
                            ->multiple()
                            ->disk('public')
                            ->directory('financeiro/anexos')
                            ->preserveFilenames()
                            ->required(),
                    ])
                    ->action(function (LancamentoFinanceiro $record, array $data) {
                        foreach ($data['arquivos'] as $file) {
                            $record->documentos()->create([
                                'nome_arquivo' => basename($file),
                                'caminho' => $file,
                                'extensao' => pathinfo($file, PATHINFO_EXTENSION),
                                'tamanho' => Storage::disk('public')->exists($file) ? Storage::disk('public')->size($file) : 0,
                                'user_id' => auth()->id(),
                            ]);
                        }
                        $this->dispatch('notify', message: 'Documentos anexados com sucesso!');
                    })
                    ->modalHeading('Anexos do Lançamento')
                    ->modalWidth('xl'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('marcar_pago_lote')
                    ->label('Marcar como Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Collection $records) => $records->each->update([
                        'status' => 'pago',
                        'data_pagamento' => now(),
                    ])),
            ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('descricao')
                ->label('Descrição')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('valor')
                ->numeric()
                ->required()
                ->prefix('R$')
                ->label('Valor'),
            Select::make('tipo')
                ->options([
                    'receita' => 'Receita',
                    'despesa' => 'Despesa',
                ])
                ->required()
                ->label('Tipo'),
            Select::make('categoria_financeira_id')
                ->label('Categoria')
                ->relationship('categoria', 'nome', fn (Builder $query) => $query->estratificado())
                ->createOptionForm([
                    TextInput::make('nome')->required(),
                    Select::make('tipo')->options(['receita' => 'Receita', 'despesa' => 'Despesa', 'ambos' => 'Ambos'])->default('ambos')->required(),
                ])
                ->required(),
            Select::make('status')
                ->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado',
                ])
                ->default('pendente')
                ->required()
                ->label('Status'),
            DatePicker::make('data_vencimento')
                ->label('Data de Vencimento')
                ->default(now())
                ->required(),
            DatePicker::make('data_pagamento')
                ->label('Data de Pagamento'),
        ];
    }

    public function exportToPdf()
    {
        // Aqui enviamos os filtros atuais para o controlador de PDF
        $filters = $this->tableFilters;

        return redirect()->route('admin.financeiro.report', [
            'filters' => $filters,
            'search' => $this->tableSearch,
        ]);
    }

    public function render(): View
    {
        return view('livewire.financeiro.financeiro-manager')
            ->layout('layouts.app');
    }
}
