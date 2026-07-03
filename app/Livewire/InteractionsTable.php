<?php

namespace App\Livewire;

use App\Models\Interacao;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class InteractionsTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Interacao::query()->latest())
            ->columns([
                TextColumn::make('tipo')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y'),
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->limit(50),
            ]);
    }

    public function render(): View
    {
        return view('livewire.interactions-table');
    }
}
