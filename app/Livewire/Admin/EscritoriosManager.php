<?php

namespace App\Livewire\Admin;

use App\Models\Escritorio;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class EscritoriosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Escritorio::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Escritório')
                    ->searchable()
                    ->sortable()
                    ->weight('black')
                    ->color('primary'),
                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cidade')
                    ->label('Cidade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uf')
                    ->label('UF')
                    ->badge()
                    ->color('slate')
                    ->searchable(),
                TextColumn::make('users_count')
                    ->label('Usuários')
                    ->counts('users')
                    ->badge()
                    ->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Escritório')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sem escritórios cadastrados')
            ->emptyStateDescription('Comece adicionando a sede ou filial da advocacia.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome do Escritório')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: Matriz São Paulo'),
            TextInput::make('cnpj')
                ->label('CNPJ (Opcional)')
                ->maxLength(20),
            TextInput::make('cidade')
                ->label('Cidade')
                ->required()
                ->maxLength(255),
            TextInput::make('uf')
                ->label('UF')
                ->required()
                ->maxLength(2)
                ->placeholder('Ex: SP'),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.escritorios-manager');
    }
}
