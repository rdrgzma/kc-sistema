<?php

namespace App\Livewire\Processo;

use App\Models\PecaProcessual;
use App\Models\Planner;
use App\Models\Processo;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class PecasRelationManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Processo $processo;

    public function mount(Processo $processo): void
    {
        $this->processo = $processo;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PecaProcessual::query()->where('processo_id', $this->processo->id)->with('autor', 'task')->latest()
            )
            ->columns([
                TextColumn::make('tipoPeca.nome')
                    ->label('Tipo de Documento / Peça')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('autor.name')
                    ->label('Autor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('data_producao')
                    ->label('Data de Produção')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('task.title')
                    ->label('Tarefa Vinculada')
                    ->placeholder('—')
                    ->limit(30)
                    ->tooltip(fn (?string $state): ?string => $state),

                TextColumn::make('documentos.nome_arquivo')
                    ->label('Documentos')
                    ->placeholder('—')
                    ->listWithLineBreaks()
                    ->bulleted(),

                TextColumn::make('observacoes')
                    ->label('Observações')
                    ->placeholder('-')
                    ->limit(50),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Registrar Documento / Peça')
                    ->form($this->getFormSchema())
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['processo_id'] = $this->processo->id;

                        // Se o campo autor_id não foi preenchido (usuário sem permissão), usa o logado
                        if (empty($data['autor_id'])) {
                            $data['autor_id'] = auth()->id();
                        }

                        return $data;
                    }),
            ])
            ->emptyStateHeading('Nenhum documento / peça registrado para este processo');
    }

    protected function getFormSchema(): array
    {
        $isGestor = auth()->user()?->hasAnyRole(['Administrador', 'Sócio']);

        return [
            Grid::make(2)
                ->schema([
                    Select::make('tipo_peca_id')
                        ->label('Tipo de Documento / Peça')
                        ->relationship('tipoPeca', 'nome')
                        ->required(),

                    DatePicker::make('data_producao')
                        ->label('Data de Produção')
                        ->required()
                        ->default(now()),

                    Select::make('autor_id')
                        ->label('Autor do Documento / Peça')
                        ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->default(auth()->id())
                        ->required()
                        ->visible($isGestor),

                    Select::make('task_id')
                        ->label('Tarefa Vinculada')
                        ->options(function () {
                            return $this->getTasksForProcesso();
                        })
                        ->searchable()
                        ->nullable()
                        ->placeholder('Sem vínculo com tarefa'),

                    Select::make('documentos')
                        ->label('Documentos Associados')
                        ->multiple()
                        ->relationship('documentos', 'nome_arquivo', function ($query) {
                            $query->where('documentable_type', Processo::class)
                                ->where('documentable_id', $this->processo->id);
                        })
                        ->preload()
                        ->columnSpanFull(),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ];
    }

    /**
     * Busca tarefas de Planners vinculados ao mesmo Processo.
     *
     * @return array<int, string>
     */
    protected function getTasksForProcesso(): array
    {
        $plannerIds = Planner::where('plannable_type', Processo::class)
            ->where('plannable_id', $this->processo->id)
            ->pluck('id');

        if ($plannerIds->isEmpty()) {
            return [];
        }

        return Task::whereHas('bucket', function ($query) use ($plannerIds) {
            $query->whereIn('planner_id', $plannerIds);
        })
            ->whereDoesntHave('pecaProcessual')
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.processo.pecas-relation-manager');
    }
}
