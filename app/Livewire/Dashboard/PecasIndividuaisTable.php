<?php

namespace App\Livewire\Dashboard;

use App\Models\PecaProcessual;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class PecasIndividuaisTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public int $userId;

    public ?string $dataInicio = null;

    public ?string $dataFim = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PecaProcessual::query()
                    ->where('autor_id', $this->userId)
                    ->when($this->dataInicio, fn ($q) => $q->whereDate('data_producao', '>=', $this->dataInicio))
                    ->when($this->dataFim, fn ($q) => $q->whereDate('data_producao', '<=', $this->dataFim))
                    ->with(['processo', 'task', 'tipoPeca'])
                    ->latest('data_producao')
            )
            ->columns([
                TextColumn::make('data_producao')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipoPeca.nome')
                    ->label('Tipo de Documento / Peça')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('processo.numero_processo')
                    ->label('Processo')
                    ->placeholder('—'),

                TextColumn::make('task.title')
                    ->label('Tarefa')
                    ->limit(30)
                    ->placeholder('—'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Nenhum documento / peça registrado no período');
    }

    public function render()
    {
        return view('livewire.dashboard.pecas-individuais-table');
    }
}
