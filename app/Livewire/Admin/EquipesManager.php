<?php

namespace App\Livewire\Admin;

use App\Models\Equipe;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class EquipesManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Equipe::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Equipe')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('escritorio.nome')
                    ->label('Escritório Sede')
                    ->badge()
                    ->color('indigo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->limit(50)
                    ->color('slate'),
                TextColumn::make('users_count')
                    ->label('Membros')
                    ->counts('users')
                    ->badge()
                    ->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova Equipe')
                    ->icon('heroicon-o-user-group')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sem equipes configuradas')
            ->emptyStateDescription('Agrupe seus advogados em equipes (ex: Cível, Trabalhista).');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome da Equipe')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: Departamento Civil'),
            TextArea::make('descricao')
                ->label('Descrição ou Objetivo')
                ->maxLength(255),
            Select::make('escritorio_id')
                ->relationship('escritorio', 'nome')
                ->label('Escritório Vinculado')
                ->required()
                ->preload()
                ->searchable(),
            Select::make('users')
                ->relationship('users', 'name')
                ->label('Membros da Equipe')
                ->multiple()
                ->preload()
                ->searchable(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.equipes-manager');
    }
}
