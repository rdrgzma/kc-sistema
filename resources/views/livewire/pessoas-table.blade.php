<?php

use App\Models\Pessoa;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Volt\Component;

new class extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Pessoa::query()->latest())
            ->columns([
                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PF' => 'info',
                        'PJ' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('nome_razao')
                    ->label('Nome / Razão Social')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('cpf_cnpj')
                    ->label('CPF / CNPJ')
                    ->searchable(),
                    
                TextColumn::make('telefone')
                    ->label('Telefone')
                    ->searchable(),
                    
            ])
            ->filters([
                // Filtros entrarão aqui
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova Pessoa')
                    ->model(Pessoa::class)
                    ->slideOver()
                    ->form(self::obterSchemaDoFormulario()), // <-- Chamada estática segura!
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->color('gray')
                    ->slideOver()
                    ->form(self::obterSchemaDoFormulario()),
                Action::make('view')
                    ->label('Ficha do Cliente')
                    ->icon('heroicon-m-user')
                    ->url(fn (Pessoa $record): string => route('pessoas.show', $record))
            ])
            ->bulkActions([
                BulkAction::make('deletar')
                    ->label('Deletar Selecionados')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ]);
    }

    /**
     * Transformamos o método em "public static" para resolver o erro de contexto ($this)
     * e o Filament conseguir injetar os campos perfeitamente.
     */
    public static function obterSchemaDoFormulario(): array
    {
        return [
            Section::make('Dados Principais')
                ->description('Informações de contato e identificação do cliente.')
                ->columns(2)
                ->schema([
                    Select::make('tipo')
                        ->options([
                            'PF' => 'Pessoa Física',
                            'PJ' => 'Pessoa Jurídica',
                        ])
                        ->default('PF')
                        ->required()
                        ->live(), 

                    TextInput::make('nome_razao')
                        ->label('Nome / Razão Social')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('cpf_cnpj')
                        ->label(fn ( $get) => $get('tipo') === 'PJ' ? 'CNPJ' : 'CPF')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('telefone')
                        ->tel()
                        ->maxLength(255),
    ]), 
    Section::make('Endereço')
                ->description('Informe o CEP para preenchimento automático.')
                ->columns(3)
                ->schema([
                    TextInput::make('cep')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->placeholder('00000-000')
                        ->live(onBlur: true) // Dispara quando o usuário sai do campo
                        ->afterStateUpdated(function ($state, Set $set) {
                            $cep = preg_replace('/[^0-9]/', '', $state);
                            
                            if (strlen($cep) !== 8) return;

                            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
                            
                            if ($response->failed()) return;

                            $data = $response->json();

                            if (isset($data['erro'])) return;

                            $set('logradouro', $data['logradouro'] ?? '');
                            $set('bairro', $data['bairro'] ?? '');
                            $set('cidade', $data['localidade'] ?? '');
                            $set('estado', $data['uf'] ?? '');
                        }),

                    TextInput::make('logradouro')
                        ->label('Logradouro')
                        ->columnSpan(2),

                    TextInput::make('numero')
                        ->label('Número'),

                    TextInput::make('complemento')
                        ->label('Complemento'),

                    TextInput::make('bairro')
                        ->label('Bairro'),

                    TextInput::make('cidade')
                        ->label('Cidade'),

                    TextInput::make('estado')
                        ->label('UF')
                        ->maxLength(2),
                ])


        ];
    }
}; 
?>

<div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-zinc-50">Gestão de Pessoas</h2>
        <p class="text-sm text-gray-500 dark:text-zinc-400">Clientes físicos e jurídicos unificados</p>
    </div>

    {{ $this->table }}
    <x-filament-actions::modals />

</div>