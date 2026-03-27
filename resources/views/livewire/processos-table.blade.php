<?php

use App\Models\Processo;
use App\Models\Pessoa;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
// Importamos as Traits e Interfaces com Aliases para evitar o conflito de nomes
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Livewire\Volt\Component;

new class extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions; // Agora o PHP aceita pois o Filament resolve internamente as precedências

    public function table(Table $table): Table
    {
        return $table
            ->query(Processo::query()->with('pessoa')->latest())
            ->columns([
                TextColumn::make('numero_processo')
                    ->label('Nº do Processo')
                    ->searchable()
                    ->copyable(),
                    
                TextColumn::make('pessoa.nome_razao')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('economia_gerada')
                    ->label('Econ. Gerada')
                    ->money('BRL')
                    ->color('success'),

                TextColumn::make('perda_estimada')
                    ->label('Perda Est.')
                    ->money('BRL')
                    ->color('danger'),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('Abrir Ficha')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Processo $record): string => route('processos.show', $record)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Processo')
                    ->model(Processo::class)
                    ->slideOver()
                    ->form(fn () => self::getFormSchema()), // Usando closure para contexto seguro
            ]);
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make('Identificação')
                ->columns(2)
                ->schema([
                    TextInput::make('numero_processo')
                        ->label('Número do Processo')
                        ->required(),

                    Select::make('pessoa_id')
                        ->label('Cliente / Pessoa')
                        ->relationship('pessoa', 'nome_razao')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Análise de Mérito (Provisionamento)')
                ->description('Dados essenciais para o Dashboard de Performance.')
                ->columns(2)
                ->schema([
                    TextInput::make('economia_gerada')
                        ->label('Economia Gerada (R$)')
                        ->numeric()
                        ->prefix('R$'),

                    TextInput::make('perda_estimada')
                        ->label('Perda Estimada (R$)')
                        ->numeric()
                        ->prefix('R$'),
                ]),
        ];
    }
};
?>

<div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-zinc-50">Gestão Processual</h2>
        <p class="text-sm text-gray-500 dark:text-zinc-400">Controle estratégico e provisionamento de mérito</p>
    </div>

    {{ $this->table }}

    <x-filament-actions::modals />
</div>