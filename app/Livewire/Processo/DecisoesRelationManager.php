<?php

namespace App\Livewire\Processo;

use App\Enums\ClassificacaoDecisao;
use App\Enums\StatusFinanceiroDecisao;
use App\Models\Processo;
use App\Models\Sentenca;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class DecisoesRelationManager extends Component implements HasActions, HasForms, HasTable
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
                Sentenca::query()->whereHas('processos', fn ($q) => $q->where('processos.id', $this->processo->id))
            )
            ->columns([
                TextColumn::make('nome')
                    ->label('Identificação')
                    ->searchable(),

                TextColumn::make('classificacao')
                    ->label('Classificação')
                    ->badge()
                    ->color(fn (ClassificacaoDecisao $state): string => match ($state) {
                        ClassificacaoDecisao::FAVORAVEL => 'success',
                        ClassificacaoDecisao::DESFAVORAVEL => 'danger',
                        ClassificacaoDecisao::PARCIAL => 'warning',
                    })
                    ->formatStateUsing(fn (ClassificacaoDecisao $state): string => $state->getLabel()),

                TextColumn::make('tipo_decisao')
                    ->label('Tipo de Decisão'),

                TextColumn::make('valor_economia')
                    ->label('Economia')
                    ->money('BRL'),

                TextColumn::make('valor_perda')
                    ->label('Perda')
                    ->money('BRL'),

                TextColumn::make('status_financeiro')
                    ->label('Status Financeiro')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (StatusFinanceiroDecisao $state): string => $state->getLabel()),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->slideOver(),
                DeleteAction::make()
                    ->action(function (Sentenca $record) {
                        // Dissociate from Processo first
                        $this->processo->update(['sentenca_id' => null]);
                        $record->delete();
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova Decisão')
                    ->form($this->getFormSchema())
                    ->slideOver()
                    ->using(function (array $data): Sentenca {
                        $sentenca = Sentenca::create($data);
                        $this->processo->update(['sentenca_id' => $sentenca->id]);

                        return $sentenca;
                    })
                    ->visible(fn (): bool => ! $this->processo->sentenca_id),
            ])
            ->emptyStateHeading('Nenhuma decisão registrada para este processo');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('nome')
                        ->label('Nome / Identificação')
                        ->placeholder('Ex: Sentença de 1º Grau')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('tipo_decisao')
                        ->label('Tipo de Decisão')
                        ->placeholder('Ex: Sentença, Acórdão')
                        ->required()
                        ->maxLength(255),

                    Select::make('classificacao')
                        ->label('Classificação')
                        ->options(ClassificacaoDecisao::class)
                        ->required()
                        ->reactive(),

                    Select::make('status_financeiro')
                        ->label('Status Financeiro')
                        ->options(StatusFinanceiroDecisao::class)
                        ->required(),

                    TextInput::make('valor_economia')
                        ->label('Valor da Economia (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0.00)
                        ->required(fn ($get) => in_array($get('classificacao'), [ClassificacaoDecisao::FAVORAVEL->value, ClassificacaoDecisao::PARCIAL->value]))
                        ->visible(fn ($get) => in_array($get('classificacao'), [ClassificacaoDecisao::FAVORAVEL->value, ClassificacaoDecisao::PARCIAL->value])),

                    TextInput::make('valor_perda')
                        ->label('Valor da Perda (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0.00)
                        ->required(fn ($get) => in_array($get('classificacao'), [ClassificacaoDecisao::DESFAVORAVEL->value, ClassificacaoDecisao::PARCIAL->value]))
                        ->visible(fn ($get) => in_array($get('classificacao'), [ClassificacaoDecisao::DESFAVORAVEL->value, ClassificacaoDecisao::PARCIAL->value])),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.processo.decisoes-relation-manager');
    }
}
