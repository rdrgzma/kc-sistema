<?php

namespace App\Livewire\Admin;

use App\Models\AssistenteTecnico;
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

class AssistentesTecnicosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(AssistenteTecnico::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Assistente Técnico')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('especialidade.nome')
                    ->label('Especialidade')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Sistema / Usuário')
                    ->placeholder('Externo')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Assistente')
                    ->icon('heroicon-o-user')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sem assistentes cadastrados')
            ->emptyStateDescription('Adicione assistentes para as perícias.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome do Assistente')
                ->required()
                ->maxLength(255),
            Select::make('especialidade_id')
                ->label('Especialidade')
                ->relationship('especialidade', 'nome')
                ->required()
                ->preload()
                ->searchable(),
            Select::make('user_id')
                ->label('Vincular a Usuário (Opcional)')
                ->relationship('user', 'name')
                ->placeholder('Selecione caso este assistente seja um advogado/membro do escritório')
                ->preload()
                ->searchable(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.assistentes-tecnicos-manager');
    }
}
