<?php

namespace App\Livewire;

use App\Models\Bucket;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Minhas Tarefas')]
class MinhasTarefas extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('assigned_to', auth()->id())
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Título da Tarefa')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('pessoa.nome_razao')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bucket.planner.name')
                    ->label('Quadro')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bucket.name')
                    ->label('Coluna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn (Task $record) => $record->due_date && $record->due_date->isPast() ? 'danger' : 'gray'),
                TextColumn::make('urgency')
                    ->label('Urgência')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('ocultar_concluidas')
                    ->label('Ocultar Concluídas')
                    ->default()
                    ->query(fn (Builder $query) => $query->whereHas('bucket', function ($q) {
                        $q->where('name', 'not like', '%completed%')
                            ->where('name', 'not like', '%done%')
                            ->where('name', 'not like', '%conclu%')
                            ->where('name', 'not like', '%finalizado%');
                    })),
            ])
            ->actions([
                Action::make('concluir')
                    ->label('Concluir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Task $record) => ! str_contains(strtolower($record->bucket->name), 'conclu') && ! str_contains(strtolower($record->bucket->name), 'done') && ! str_contains(strtolower($record->bucket->name), 'completed') && ! str_contains(strtolower($record->bucket->name), 'finalizado'))
                    ->action(function (Task $record) {
                        $completedBucket = Bucket::where('planner_id', $record->bucket->planner_id)
                            ->where(function ($q) {
                                $q->where('name', 'like', '%completed%')
                                    ->orWhere('name', 'like', '%done%')
                                    ->orWhere('name', 'like', '%conclu%')
                                    ->orWhere('name', 'like', '%finalizado%');
                            })
                            ->first();

                        if ($completedBucket) {
                            $record->update(['bucket_id' => $completedBucket->id]);
                        } else {
                            $lastBucket = Bucket::where('planner_id', $record->bucket->planner_id)
                                ->orderBy('id', 'desc')
                                ->first();
                            if ($lastBucket) {
                                $record->update(['bucket_id' => $lastBucket->id]);
                            }
                        }

                        Notification::make()
                            ->title('Tarefa concluída com sucesso!')
                            ->success()
                            ->send();
                    }),
                Action::make('reabrir')
                    ->label('Reabrir')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Task $record) => str_contains(strtolower($record->bucket->name), 'conclu') || str_contains(strtolower($record->bucket->name), 'done') || str_contains(strtolower($record->bucket->name), 'completed') || str_contains(strtolower($record->bucket->name), 'finalizado'))
                    ->action(function (Task $record) {
                        $firstBucket = Bucket::where('planner_id', $record->bucket->planner_id)
                            ->where(function ($q) {
                                $q->where('name', 'not like', '%completed%')
                                    ->where('name', 'not like', '%done%')
                                    ->where('name', 'not like', '%conclu%')
                                    ->where('name', 'not like', '%finalizado%');
                            })
                            ->orderBy('sort', 'asc')
                            ->first();

                        if (! $firstBucket) {
                            $firstBucket = Bucket::where('planner_id', $record->bucket->planner_id)
                                ->orderBy('sort', 'asc')
                                ->first();
                        }

                        if ($firstBucket) {
                            $record->update(['bucket_id' => $firstBucket->id]);
                        }

                        Notification::make()
                            ->title('Tarefa reaberta com sucesso!')
                            ->info()
                            ->send();
                    }),
                Action::make('ver_quadro')
                    ->label('Ver no Quadro')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->color('gray')
                    ->url(fn (Task $record) => route('planners.index', ['p' => $record->bucket->planner_id])),
            ])
            ->emptyStateHeading('Sem tarefas atribuídas');
    }

    public function render(): View
    {
        return view('livewire.minhas-tarefas');
    }
}
