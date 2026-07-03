<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use App\Models\User;
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

class ProdutividadeTarefasRankingTable extends Component implements HasActions, HasForms, HasTable
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
                    ->select('users.*')
                    ->selectSub(
                        Task::query()
                            ->selectRaw('count(*)')
                            ->whereColumn('tasks.assigned_to', 'users.id')
                            ->whereHas('bucket', function ($q) {
                                $q->where('name', 'like', '%completed%')
                                    ->orWhere('name', 'like', '%done%')
                                    ->orWhere('name', 'like', '%conclu%');
                            })
                            ->when($this->dataInicio, fn ($q) => $q->whereDate('created_at', '>=', $this->dataInicio))
                            ->when($this->dataFim, fn ($q) => $q->whereDate('created_at', '<=', $this->dataFim)),
                        'tarefas_concluidas_count'
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Membro da Equipe')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tarefas_concluidas_count')
                    ->label('Tarefas Concluídas')
                    ->badge()
                    ->color('success')
                    ->suffix(' tarefas')
                    ->sortable(),
            ])
            ->defaultSort('tarefas_concluidas_count', 'desc')
            ->emptyStateHeading('Sem tarefas concluídas no período selecionado');
    }

    public function render()
    {
        return view('livewire.dashboard.produtividade-tarefas-ranking-table');
    }
}
