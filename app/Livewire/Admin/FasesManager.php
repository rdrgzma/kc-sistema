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
    use InteractsWithActions, InteractsWithForms, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Fase::query())
            ->columns([
                TextColumn::make('nome')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                TextColumn::make('valor_custa_padrao')
                    ->money('BRL')
                    ->sortable()
                    ->label('Custa Padrão'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('valor_custa_padrao')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0.00),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('valor_custa_padrao')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0.00),
                    ]),
                DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.admin.fases-manager');
    }
}
