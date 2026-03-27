<?php

use App\Models\Processo;
use App\Models\Pessoa;
use App\Models\Seguradora;
use App\Models\Area;
use App\Models\Fase;
use App\Models\Procedimento;
use App\Models\Sentenca;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Livewire\Volt\Component;

new class extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Processo::query()->with(['pessoa', 'seguradora', 'fase', 'area'])->latest())
            ->columns([
                TextColumn::make('numero_processo')
                    ->label('Nº do Processo')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                    
                TextColumn::make('pessoa.nome_razao')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('seguradora.nome')
                    ->label('Seguradora')
                    ->toggleable(),

                TextColumn::make('area.nome')
                    ->label('Área')
                    ->toggleable(),

                TextColumn::make('fase.nome')
                    ->label('Fase Atual')
                    ->badge()
                    ->color('info'),

                TextColumn::make('economia_gerada')
                    ->label('Econ. Gerada')
                    ->money('BRL')
                    ->color('success')
                    ->alignment('right'),

                TextColumn::make('perda_estimada')
                    ->label('Perda Est.')
                    ->money('BRL')
                    ->color('danger')
                    ->alignment('right'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->slideOver()
                    ->form(fn () => self::getFormSchema()),

                \Filament\Actions\Action::make('view')
                    ->label('Visualizar')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Processo $record): string => route('processos.show', $record)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Processo')
                    ->model(Processo::class)
                    ->slideOver()
                    ->form(fn () => self::getFormSchema()),
            ]);
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make('Identificação Principal')
                ->description('Dados primários de identificação e clientes.')
                ->icon('heroicon-o-identification')
                ->columns(2)
                ->schema([
                    TextInput::make('numero_processo')
                        ->label('Nº do Processo')
                        ->placeholder('Ex: 0001234-56.2024.8.26.0000')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Select::make('pessoa_id')
                        ->label('Cliente')
                        ->relationship('pessoa', 'nome_razao')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Classificação Estratégica')
                ->description('Vínculos com entidades e fases do processo.')
                ->icon('heroicon-o-tag')
                ->columns(2)
                ->schema([
                    Select::make('seguradora_id')
                        ->label('Seguradora')
                        ->relationship('seguradora', 'nome')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('nome')
                                ->required()
                                ->maxLength(255),
                        ]),

                    Select::make('area_id')
                        ->label('Área Jurídica')
                        ->relationship('area', 'nome')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('nome')
                                ->required()
                                ->maxLength(255),
                        ]),

                    Select::make('fase_id')
                        ->label('Fase Atual')
                        ->relationship('fase', 'nome')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('nome')
                                ->required()
                                ->maxLength(255),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Select::make('procedimento_id')
                                ->label('Procedimento')
                                ->relationship('procedimento', 'nome')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('nome')
                                        ->required()
                                        ->maxLength(255),
                                ]),

                            Select::make('sentenca_id')
                                ->label('Sentença / Desfecho')
                                ->relationship('sentenca', 'nome')
                                ->searchable()
                                ->preload(),
                        ])
                ]),

            Section::make('Análise de Mérito e Provisionamento')
                ->description('Indicadores financeiros vitais para o Dashboard.')
                ->icon('heroicon-o-currency-dollar')
                ->columns(2)
                ->schema([
                    TextInput::make('economia_gerada')
                        ->label('Economia Gerada')
                        ->helperText('Valor economizado para o cliente.')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),

                    TextInput::make('perda_estimada')
                        ->label('Riscos / Perda Estimada')
                        ->helperText('Valor provisionado em caso de derrota.')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),
                ]),
        ];
    }
};
?>

<div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-zinc-50">Gestão de Processos</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Controle total de prazos, mérito e performance processual.</p>
        </div>
    </div>

    {{ $this->table }}

    <x-filament-actions::modals />
</div>