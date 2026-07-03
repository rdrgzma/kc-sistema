<?php

namespace App\Livewire\Admin;

use App\Models\Fase;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class FasesManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Fase::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Fluxo / Fase')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('valor_custa_padrao')
                    ->label('Custa Padrão')
                    ->money('BRL')
                    ->sortable()
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova Fase')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sem fases cadastradas');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome da Fase')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: Conhecimento, Execução...'),
            TextInput::make('valor_custa_padrao')
                ->label('Valor de Custa Padrão')
                ->numeric()
                ->prefix('R$')
                ->default(0.00),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.fases-manager');
    }
}
