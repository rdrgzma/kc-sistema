<?php

namespace App\Livewire\Admin;

use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Tabs;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use App\Models\Equipe;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                Action::make('manage_permissions')
                    ->label('Permissões')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->fillForm(function (Equipe $record) {
                        $roleName = 'equipe_' . $record->id;
                        $role = Role::where('name', $roleName)->first();
                        if (!$role) return [];
                        
                        $rolePerms = $role->permissions()->pluck('name')->toArray();
                        $groupedPerms = Permission::orderBy('name')->get()->groupBy(function ($perm) {
                            $parts = explode('_', $perm->name);
                            return end($parts);
                        });
                        
                        $data = [];
                        foreach ($groupedPerms as $group => $perms) {
                            $data["group_{$group}"] = collect($perms)->filter(function ($p) use ($rolePerms) {
                                return in_array($p->name, $rolePerms);
                            })->pluck('name')->toArray();
                        }
                        return $data;
                    })
                    ->form(function () {
                        $groupedPerms = Permission::orderBy('name')->get()->groupBy(function ($perm) {
                            $parts = explode('_', $perm->name);
                            return end($parts);
                        });

                        $tabs = [];
                        foreach ($groupedPerms as $group => $perms) {
                            $options = [];
                            foreach ($perms as $perm) {
                                $label = str_replace('_' . $group, '', $perm->name);
                                $label = Str::headline($label);
                                $options[$perm->name] = $label;
                            }

                            $tabs[] = Tabs\Tab::make(Str::headline($group))
                                ->schema([
                                    CheckboxList::make("group_{$group}")
                                        ->hiddenLabel()
                                        ->options($options)
                                        ->columns(2)
                                        ->gridDirection('row'),
                                ])
                                ->badge(count($perms));
                        }

                        return [
                            Tabs::make('Permissions Tabs')
                                ->tabs($tabs)
                                ->activeTab(1)
                                ->contained(false)
                        ];
                    })
                    ->action(function (Equipe $record, array $data): void {
                        $allSelected = [];
                        
                        foreach ($data as $key => $values) {
                            if (str_starts_with($key, 'group_') && is_array($values)) {
                                $allSelected = array_merge($allSelected, $values);
                            }
                        }
                        
                        $roleName = 'equipe_' . $record->id;
                        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                        $oldPermissions = $role->permissions()->pluck('name')->toArray();
                        
                        $role->syncPermissions($allSelected);

                        activity()
                            ->performedOn($record)
                            ->event('updated')
                            ->withProperties([
                                'attributes' => ['permissoes' => implode(', ', $allSelected)],
                                'old' => ['permissoes' => implode(', ', $oldPermissions)]
                            ])
                            ->log('Permissões da equipa atualizadas');
                    })
                    ->modalWidth('4xl'),
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
            Textarea::make('descricao')
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
