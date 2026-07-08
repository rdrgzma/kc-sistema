<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class UserPermissionsMatrixManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with('permissions', 'roles'))
            ->columns([
                TextColumn::make('name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Papéis / Equipes')
                    ->badge()
                    ->color('info'),
                TextColumn::make('permissions_count')
                    ->label('Permissões Diretas')
                    ->counts('permissions')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
            ])
            ->actions([
                Action::make('manage_permissions')
                    ->label('Gerir Permissões')
                    ->icon('heroicon-o-shield-check')
                    ->modalHeading(fn (User $record) => 'Permissões Diretas: '.$record->name)
                    ->modalWidth('4xl')
                    ->modalDescription('As permissões selecionadas aqui são atribuídas DIRETAMENTE ao utilizador, sobrepondo-se às Equipes/Papéis.')
                    ->fillForm(function (User $record): array {
                        $data = [];
                        // Preenche os checkboxes com as permissões atuais
                        $userPerms = $record->permissions->pluck('name')->toArray();

                        $groupedPerms = Permission::all()->groupBy(function ($perm) {
                            $parts = explode('_', $perm->name);

                            return end($parts);
                        });

                        foreach ($groupedPerms as $group => $perms) {
                            $data["group_{$group}"] = collect($perms)->filter(function ($p) use ($userPerms) {
                                return in_array($p->name, $userPerms);
                            })->pluck('name')->toArray();
                        }

                        return $data;
                    })
                    ->form(function () {
                        $groupedPerms = Permission::orderBy('name')->get()->groupBy(function ($perm) {
                            // Extrai a última palavra (ex: view_any_task -> task)
                            $parts = explode('_', $perm->name);

                            return end($parts);
                        });

                        $tabs = [];
                        foreach ($groupedPerms as $group => $perms) {
                            $options = [];
                            foreach ($perms as $perm) {
                                // Transforma 'view_any_task' para 'View Any'
                                $label = str_replace('_'.$group, '', $perm->name);
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
                                ->contained(false),
                        ];
                    })
                    ->action(function (User $record, array $data): void {
                        $allSelected = [];

                        foreach ($data as $key => $values) {
                            if (str_starts_with($key, 'group_') && is_array($values)) {
                                $allSelected = array_merge($allSelected, $values);
                            }
                        }

                        $oldPermissions = $record->getDirectPermissions()->pluck('name')->toArray();

                        // Sincroniza as permissões diretas do utilizador
                        $record->syncPermissions($allSelected);

                        activity()
                            ->performedOn($record)
                            ->event('updated')
                            ->withProperties([
                                'attributes' => ['permissoes_diretas' => implode(', ', $allSelected)],
                                'old' => ['permissoes_diretas' => implode(', ', $oldPermissions)],
                            ])
                            ->log('Permissões diretas do utilizador atualizadas');
                    })
                    ->modalSubmitActionLabel('Salvar Permissões'),
            ]);
    }

    public function render()
    {
        return view('livewire.admin.user-permissions-matrix-manager');
    }
}
