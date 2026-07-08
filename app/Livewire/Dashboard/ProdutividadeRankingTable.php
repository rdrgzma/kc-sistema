<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ProdutividadeRankingTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Reactive]
    public ?string $dataInicio = null;

    #[Reactive]
    public ?string $dataFim = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withCount(['pecasProcessuais' => function ($q) {
                        $q->when($this->dataInicio, fn ($q) => $q->whereDate('data_producao', '>=', $this->dataInicio))
                            ->when($this->dataFim, fn ($q) => $q->whereDate('data_producao', '<=', $this->dataFim));
                    }])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Membro da Equipe')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->action('verResumo')
                    ->color('primary'),

                TextColumn::make('pecas_processuais_count')
                    ->label('Peças Produzidas')
                    ->badge()
                    ->color('primary')
                    ->suffix(' peças')
                    ->sortable(),
            ])
            ->defaultSort('pecas_processuais_count', 'desc')
            ->emptyStateHeading('Sem produção registrada no período selecionado')
            ->actions([
                Action::make('verResumo')
                    ->label('Resumo por Tipo')
                    ->icon('heroicon-o-chart-pie')
                    ->modalHeading(fn (User $record) => "Resumo de Produção: {$record->name}")
                    ->modalContent(function (User $record) {
                        $resumo = clone $record->pecasProcessuais()
                            ->when($this->dataInicio, fn ($q) => $q->whereDate('data_producao', '>=', $this->dataInicio))
                            ->when($this->dataFim, fn ($q) => $q->whereDate('data_producao', '<=', $this->dataFim))
                            ->selectRaw('tipo_peca_id, count(*) as total')
                            ->groupBy('tipo_peca_id')
                            ->with('tipoPeca')
                            ->get();

                        return view('livewire.dashboard.partials.resumo-producao', [
                            'resumo' => $resumo,
                            'user' => $record,
                            'dataInicio' => $this->dataInicio,
                            'dataFim' => $this->dataFim,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                Action::make('verIndividuais')
                    ->label('Ver Peças')
                    ->icon('heroicon-o-list-bullet')
                    ->modalHeading(fn (User $record) => "Peças Produzidas: {$record->name}")
                    ->modalContent(fn (User $record) => view('livewire.dashboard.pecas-individuais-modal', [
                        'userId' => $record->id,
                        'dataInicio' => $this->dataInicio,
                        'dataFim' => $this->dataFim,
                    ]))
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),
            ]);
    }

    public function verResumo(User $record): void
    {
        $this->mountTableAction('verResumo', $record);
    }

    public function render()
    {
        return view('livewire.dashboard.produtividade-ranking-table');
    }
}
