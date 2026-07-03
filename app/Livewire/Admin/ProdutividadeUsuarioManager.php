<?php

namespace App\Livewire\Admin;

use App\Exports\ProdutividadeExport;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ProdutividadeUsuarioManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions, InteractsWithForms, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->modifyQueryUsing(function (Builder $query) {
                $data = $this->tableFilters['periodo'] ?? [];

                $start = ! empty($data['data_inicial']) ? Carbon::parse($data['data_inicial'])->startOfDay() : null;
                $end = ! empty($data['data_final']) ? Carbon::parse($data['data_final'])->endOfDay() : null;

                $query->withCount([
                    'tasks as total_tarefas_atribuidas' => function ($q) use ($start, $end) {
                        if ($start) {
                            $q->where('created_at', '>=', $start);
                        }
                        if ($end) {
                            $q->where('created_at', '<=', $end);
                        }
                    },
                    'tasks as tarefas_concluidas' => function ($q) use ($start, $end) {
                        $q->whereHas('bucket', function ($bq) {
                            $bq->where('name', 'like', '%completed%')
                                ->orWhere('name', 'like', '%done%')
                                ->orWhere('name', 'like', '%conclu%');
                        })->where(function ($qq) use ($start, $end) {
                            if ($start) {
                                $qq->where('created_at', '>=', $start);
                            }
                            if ($end) {
                                $qq->where('created_at', '<=', $end);
                            }
                        });
                    },
                ]);

                $query->withAvg([
                    'tasks as media_inicios_por_tarefa' => function ($q) use ($start, $end) {
                        if ($start) {
                            $q->where('created_at', '>=', $start);
                        }
                        if ($end) {
                            $q->where('created_at', '<=', $end);
                        }
                    },
                ], 'inicios_count');

                $query->withSum([
                    'tasks as soma_conclusoes' => function ($q) use ($start, $end) {
                        if ($start) {
                            $q->where('created_at', '>=', $start);
                        }
                        if ($end) {
                            $q->where('created_at', '<=', $end);
                        }
                    },
                ], 'conclusoes_count');

                $query->with(['apontamentosTempo' => function ($q) use ($start, $end) {
                    if ($start) {
                        $q->where('data_atividade', '>=', $start);
                    }
                    if ($end) {
                        $q->where('data_atividade', '<=', $end);
                    }
                }]);
            })
            ->filters([
                Filter::make('periodo')
                    ->form([
                        DatePicker::make('data_inicial')->label('Data Inicial')->default(now()->startOfMonth()),
                        DatePicker::make('data_final')->label('Data Final')->default(now()->endOfMonth()),
                    ])
                    ->query(fn (Builder $query) => $query),
            ])
            ->headerActions([
                Action::make('exportar_excel')
                    ->label('Exportar Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (ProdutividadeUsuarioManager $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();

                        return Excel::download(new ProdutividadeExport($records), 'produtividade-'.date('Ymd').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                    }),
                Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->color('danger')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (ProdutividadeUsuarioManager $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();

                        return Excel::download(new ProdutividadeExport($records), 'produtividade-'.date('Ymd').'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
                    }),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_tarefas_atribuidas')
                    ->label('Tarefas Atribuídas')
                    ->sortable(),

                TextColumn::make('tarefas_concluidas')
                    ->label('Tarefas Concluídas')
                    ->sortable(),

                TextColumn::make('media_inicios_por_tarefa')
                    ->label('Média de Inícios/Tarefa')
                    ->numeric(2)
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', '.')),

                TextColumn::make('taxa_retrabalho')
                    ->label('Taxa de Retrabalho')
                    ->state(function (User $record) {
                        $conclusoes = $record->soma_conclusoes ?? 0;
                        $concluidas = $record->tarefas_concluidas ?? 0;

                        if ($concluidas == 0) {
                            return '0';
                        }

                        $taxa = $conclusoes / $concluidas;
                        $retrabalhosExtras = $conclusoes - $concluidas;

                        $formatado = number_format($taxa, 2, ',', '.');

                        return "{$formatado} (".($retrabalhosExtras > 0 ? "+{$retrabalhosExtras}" : '0').')';
                    }),

                TextColumn::make('repeticoes')
                    ->label('Repetições')
                    ->state(function (User $record) {
                        $conclusoes = $record->soma_conclusoes ?? 0;
                        $concluidas = $record->tarefas_concluidas ?? 0;
                        $repeticoes = $conclusoes - $concluidas;

                        return $repeticoes > 0 ? $repeticoes : 0;
                    }),

                TextColumn::make('tempo_total')
                    ->label('Tempo Total Apontado')
                    ->state(function (User $record) {
                        $totalMinutos = $record->apontamentosTempo->sum('tempo_deslocamento');

                        if ($totalMinutos === 0) {
                            return '-';
                        }

                        $horas = floor($totalMinutos / 60);
                        $minutos = $totalMinutos % 60;

                        return "{$horas}h {$minutos}m";
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.admin.produtividade-usuario-manager');
    }
}
