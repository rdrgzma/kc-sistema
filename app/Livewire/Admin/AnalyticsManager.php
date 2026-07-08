<?php

namespace App\Livewire;

namespace App\Livewire\Admin;

use App\Models\Equipe;
use App\Models\Pessoa;
use App\Models\Processo;
use App\Models\Task;
use App\Models\TimelineEvent;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
class AnalyticsManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->with(['causer', 'subject'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('Operador')
                    ->placeholder('Sistema')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Ação / Descrição')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Registro Alvo')
                    ->formatStateUsing(function (Activity $record) {
                        if (! $record->subject) {
                            return $record->subject_type
                                ? class_basename($record->subject_type).' #'.$record->subject_id
                                : 'Não disponível';
                        }

                        $class = class_basename($record->subject_type);
                        $name = '';

                        if ($record->subject instanceof Pessoa) {
                            $name = $record->subject->nome_razao;
                        } elseif ($record->subject instanceof Processo) {
                            $name = $record->subject->numero_processo;
                        } else {
                            $name = $record->subject->nome
                                ?? $record->subject->name
                                ?? $record->subject->titulo
                                ?? $record->subject->title
                                ?? $record->subject->descricao
                                ?? $record->subject->tipo
                                ?? '#'.$record->subject_id;
                        }

                        return "{$class}: {$name}";
                    })
                    ->url(function (Activity $record) {
                        if (! $record->subject) {
                            return null;
                        }

                        if ($record->subject instanceof Pessoa) {
                            return route('pessoas.show', $record->subject_id);
                        }

                        if ($record->subject instanceof Processo) {
                            return route('processos.show', $record->subject_id);
                        }

                        if ($record->subject instanceof User) {
                            return route('admin.users');
                        }

                        if ($record->subject instanceof Equipe) {
                            return route('admin.equipes');
                        }

                        if ($record->subject instanceof Task) {
                            return route('agenda.index');
                        }

                        if ($record->subject instanceof TimelineEvent) {
                            $parent = $record->subject->timelineable;
                            if ($parent instanceof Processo) {
                                return route('processos.show', $parent->id);
                            }
                            if ($parent instanceof Pessoa) {
                                return route('pessoas.show', $parent->id);
                            }
                        }

                        return null;
                    })
                    ->color(fn (Activity $record) => $record->subject ? 'primary' : 'gray')
                    ->weight(fn (Activity $record) => $record->subject ? 'bold' : 'normal'),
            ])
            ->filters([
                SelectFilter::make('causer_id')
                    ->label('Operador')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('subject_type')
                    ->label('Tipo de Registro')
                    ->options(function () {
                        return Activity::query()
                            ->whereNotNull('subject_type')
                            ->distinct()
                            ->pluck('subject_type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->toArray();
                    }),

                Filter::make('created_at')
                    ->form([
                        DateTimePicker::make('data_de')
                            ->label('De'),
                        DateTimePicker::make('data_ate')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['data_de'], fn ($q) => $q->where('created_at', '>=', $data['data_de']))
                            ->when($data['data_ate'], fn ($q) => $q->where('created_at', '<=', $data['data_ate']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['data_de'] ?? null) {
                            $indicators[] = 'De: '.Carbon::parse($data['data_de'])->format('d/m/Y H:i');
                        }
                        if ($data['data_ate'] ?? null) {
                            $indicators[] = 'Até: '.Carbon::parse($data['data_ate'])->format('d/m/Y H:i');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('viewChanges')
                    ->label('Ver Alterações')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Histórico de Alterações')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('xl')
                    ->modalContent(fn (Activity $record) => view('components.activity-changes', ['record' => $record]))
                    ->visible(fn (Activity $record) => ! empty($record->properties->get('attributes'))),
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Exportar Excel/CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => $this->exportCsv()),
                Action::make('export_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(fn () => $this->exportPdf()),
            ])
            ->emptyStateHeading('Nenhuma atividade registrada');
    }

    public function exportCsv()
    {
        return redirect()->route('admin.analytics.export.csv', [
            'filters' => $this->tableFilters,
            'search' => $this->tableSearch,
        ]);
    }

    public function exportPdf()
    {
        return redirect()->route('admin.analytics.export.pdf', [
            'filters' => $this->tableFilters,
            'search' => $this->tableSearch,
        ]);
    }

    public function render(): View
    {
        return view('livewire.admin.analytics-manager');
    }
}
