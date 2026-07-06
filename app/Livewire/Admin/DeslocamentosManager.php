<?php

namespace App\Livewire\Admin;

use App\Enums\ModalidadeAtividade;
use App\Enums\TipoAtividadeDeslocamento;
use App\Exports\DeslocamentosExport;
use App\Models\ApontamentoTempo;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DeslocamentosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ApontamentoTempo::query()
                    ->with(['user', 'processo'])
                    ->where('modalidade', ModalidadeAtividade::PRESENCIAL)
                    ->latest()
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Membro da Equipe')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('processo.numero_processo')
                    ->label('Nº do Processo')
                    ->placeholder('Não associado')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo_atividade')
                    ->label('Tipo de Atividade')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (TipoAtividadeDeslocamento $state): string => $state->getLabel()),

                TextColumn::make('local')
                    ->label('Local de Destino')
                    ->searchable(),

                TextColumn::make('data_atividade')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Início')
                    ->time('H:i'),

                TextColumn::make('hora_fim')
                    ->label('Fim')
                    ->time('H:i'),

                TextColumn::make('tempo_deslocamento')
                    ->label('Duração')
                    ->state(fn (ApontamentoTempo $record): int => $record->tempo_deslocamento)
                    ->suffix(' min')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Membro da Equipe')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('data_atividade')
                    ->form([
                        DatePicker::make('data_de')
                            ->label('Data de')->default(now()->startOfMonth()),
                        DatePicker::make('data_ate')
                            ->label('Data até')->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['data_de'], fn ($q) => $q->whereDate('data_atividade', '>=', $data['data_de']))
                            ->when($data['data_ate'], fn ($q) => $q->whereDate('data_atividade', '<=', $data['data_ate']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['data_de'] ?? null) {
                            $indicators[] = 'De: '.Carbon::parse($data['data_de'])->format('d/m/Y');
                        }
                        if ($data['data_ate'] ?? null) {
                            $indicators[] = 'Até: '.Carbon::parse($data['data_ate'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Deslocamento')
                    ->form($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['modalidade'] = ModalidadeAtividade::PRESENCIAL->value;

                        return $data;
                    })
                    ->slideOver(),
                Action::make('exportar_excel')
                    ->label('Exportar Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (DeslocamentosManager $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();

                        return Excel::download(new DeslocamentosExport($records), 'deslocamentos-'.date('Ymd').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                    }),
                Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->color('danger')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (DeslocamentosManager $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();

                        return Excel::download(new DeslocamentosExport($records), 'deslocamentos-'.date('Ymd').'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
                    }),
            ])
            ->emptyStateHeading('Sem deslocamentos registrados');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('user_id')
                        ->label('Membro da Equipe')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(auth()->id()),

                    Select::make('processo_id')
                        ->label('Processo')
                        ->relationship(
                            name: 'processo',
                            titleAttribute: 'numero_processo',
                            modifyQueryUsing: fn (Builder $query) => $query->whereNotNull('numero_processo'),
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('tipo_atividade')
                        ->label('Tipo de Atividade')
                        ->options(TipoAtividadeDeslocamento::class)
                        ->required(),

                    TextInput::make('local')
                        ->label('Local de Destino')
                        ->placeholder('Ex: Fórum Central, Reunião com Cliente')
                        ->maxLength(255)
                        ->required(),

                    DatePicker::make('data_atividade')
                        ->label('Data do Deslocamento')
                        ->required()
                        ->default(now()),

                    TextInput::make('hora_inicio')
                        ->label('Hora de Início')
                        ->type('time')
                        ->required()
                        ->live(),

                    TextInput::make('hora_fim')
                        ->label('Hora de Fim')
                        ->type('time')
                        ->required()
                        ->live(),

                    Placeholder::make('tempo_deslocamento_calculado')
                        ->label('Duração do Deslocamento')
                        ->content(function ($get) {
                            $inicio = $get('hora_inicio');
                            $fim = $get('hora_fim');
                            if (! $inicio || ! $fim) {
                                return '0 minutos';
                            }
                            try {
                                $start = Carbon::parse($inicio);
                                $end = Carbon::parse($fim);
                                $diff = $start->diffInMinutes($end);

                                return "{$diff} minutos";
                            } catch (\Exception $e) {
                                return '0 minutos';
                            }
                        }),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.deslocamentos-manager');
    }
}
