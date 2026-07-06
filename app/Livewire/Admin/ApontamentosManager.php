<?php

namespace App\Livewire\Admin;

use App\Enums\ModalidadeAtividade;
use App\Enums\TipoAtividadeDeslocamento;
use App\Filament\Exports\ApontamentoExporter;
use App\Models\ApontamentoTempo;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ApontamentosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(ApontamentoTempo::query()->with(['user', 'processo'])->latest())
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

                TextColumn::make('modalidade')
                    ->label('Modalidade')
                    ->badge()
                    ->color(fn (ModalidadeAtividade $state): string => $state === ModalidadeAtividade::ONLINE ? 'success' : 'warning')
                    ->formatStateUsing(fn (ModalidadeAtividade $state): string => $state->getLabel()),

                TextColumn::make('local')
                    ->label('Local')
                    ->placeholder('-')
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
                    ->label('Tempo (Minutos)')
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
                            ->label('Data de'),
                        DatePicker::make('data_ate')
                            ->label('Data até'),
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
                    ->label('Novo Apontamento')
                    ->form($this->getFormSchema())
                    ->slideOver(),
                ExportAction::make()
                    ->label('Exportar Excel/CSV')
                    ->exporter(ApontamentoExporter::class)
                    ->color('warning'),
            ])
            ->emptyStateHeading('Sem apontamentos registrados');
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

                    Select::make('modalidade')
                        ->label('Modalidade')
                        ->options(ModalidadeAtividade::class)
                        ->required()
                        ->reactive(),

                    TextInput::make('local')
                        ->label('Local')
                        ->placeholder('Ex: Fórum Central, Reunião com Cliente')
                        ->maxLength(255)
                        ->required(fn ($get) => $get('modalidade') === ModalidadeAtividade::PRESENCIAL->value)
                        ->visible(fn ($get) => $get('modalidade') === ModalidadeAtividade::PRESENCIAL->value),

                    DatePicker::make('data_atividade')
                        ->label('Data da Atividade')
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
                        ->label('Tempo Estimado')
                        ->content(function ($get) {
                            $inicio = $get('hora_inicio');
                            $fim = $get('hora_fim');
                            if ($inicio && $fim) {
                                try {
                                    $start = Carbon::parse($inicio);
                                    $end = Carbon::parse($fim);
                                    $diff = (int) $start->diffInMinutes($end);

                                    return "{$diff} minutos";
                                } catch (\Exception $e) {
                                    return '-';
                                }
                            }

                            return '-';
                        }),

                    Textarea::make('descricao')
                        ->label('Descrição / Observações')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.apontamentos-manager');
    }
}
