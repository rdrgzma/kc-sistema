<?php

namespace App\Livewire\Admin;

use App\Models\Perito;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class PeritosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Perito::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome do Perito')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('especialidade.nome')
                    ->label('Especialidade')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Usuário Vinculado')
                    ->placeholder('Não vinculado')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Perito')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Nenhum perito cadastrado')
            ->emptyStateDescription('Cadastre peritos judiciais aqui.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome do Perito')
                ->required()
                ->maxLength(255),
            Select::make('especialidade_id')
                ->label('Especialidade')
                ->relationship('especialidade', 'nome')
                ->required()
                ->preload()
                ->searchable(),
            Select::make('user_id')
                ->label('Usuário Vinculado (Opcional)')
                ->relationship('user', 'name')
                ->placeholder('Selecione se o perito já for um usuário do sistema')
                ->preload()
                ->searchable(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.peritos-manager');
    }
}
