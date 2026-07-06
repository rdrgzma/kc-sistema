<?php

namespace App\Livewire;

use App\Models\Agendamento;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class AgendaManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?int $userId = null;

    protected $queryString = ['userId'];

    public function mount(): void
    {
        if (request()->query('user_id')) {
            $this->userId = (int) request()->query('user_id');
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Agendamento::query()
                    ->with(['user', 'processo'])
                    ->orderBy('starts_at', 'desc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Título do Compromisso')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Responsável')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('processo.numero_processo')
                    ->label('Nº do Processo')
                    ->placeholder('Não associado')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Responsável')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default($this->userId),

                Filter::make('starts_at')
                    ->form([
                        DateTimePicker::make('data_de')
                            ->label('Início de'),
                        DateTimePicker::make('data_ate')
                            ->label('Fim até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['data_de'], fn ($q) => $q->where('starts_at', '>=', $data['data_de']))
                            ->when($data['data_ate'], fn ($q) => $q->where('ends_at', '<=', $data['data_ate']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['data_de'] ?? null) {
                            $indicators[] = 'Início de: '.Carbon::parse($data['data_de'])->format('d/m/Y H:i');
                        }
                        if ($data['data_ate'] ?? null) {
                            $indicators[] = 'Fim até: '.Carbon::parse($data['data_ate'])->format('d/m/Y H:i');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $user = User::find($data['user_id']);
                        if ($user) {
                            $data['escritorio_id'] = $user->escritorio_id;
                        }

                        return $data;
                    })
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Agendamento')
                    ->form($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $user = User::find($data['user_id']);
                        if ($user) {
                            $data['escritorio_id'] = $user->escritorio_id;
                        }

                        return $data;
                    })
                    ->slideOver(),
            ])
            ->emptyStateHeading('Sem compromissos agendados');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->placeholder('Ex: Reunião com Cliente, Audiência de Instrução')
                        ->maxLength(255)
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Descrição')
                        ->placeholder('Ex: Detalhes sobre a reunião ou pauta do dia')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),

                    Select::make('user_id')
                        ->label('Responsável')
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

                    DateTimePicker::make('starts_at')
                        ->label('Data/Hora de Início')
                        ->required()
                        ->default(now()->roundMinute(30)),

                    DateTimePicker::make('ends_at')
                        ->label('Data/Hora de Fim')
                        ->required()
                        ->default(now()->addHour()->roundMinute(30)),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.agenda-manager');
    }
}
