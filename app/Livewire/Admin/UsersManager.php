<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UsersManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Papéis')
                    ->badge()
                    ->searchable(),
                TextColumn::make('escritorio.nome')
                    ->label('Escritório')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('equipes.nome')
                    ->label('Equipes')
                    ->badge()
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Ativo'),
                TextColumn::make('deleted_at')
                    ->label('Lixeira')
                    ->badge()
                    ->placeholder('Não')
                    ->formatStateUsing(fn ($state) => $state ? 'Sim' : '-')
                    ->color(fn ($state) => $state ? 'danger' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Usuário')
                    ->form($this->getFormSchema()),
            ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome Completo')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->maxLength(255),
            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->label('Permissões / Papéis'),
            Select::make('escritorio_id')
                ->relationship('escritorio', 'nome')
                ->searchable()
                ->preload()
                ->label('Escritório Vinculado'),
            Select::make('equipes')
                ->relationship('equipes', 'nome')
                ->multiple()
                ->preload()
                ->label('Equipes de Trabalho'),
            Toggle::make('is_active')
                ->label('Usuário Ativo')
                ->default(true),
        ];
    }

    public function render()
    {
        return view('livewire.admin.users-manager');
    }
}
