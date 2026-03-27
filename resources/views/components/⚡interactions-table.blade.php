<?php

use App\Models\Interacao;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

new class extends Component implements HasForms, HasTable, HasActions
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
};
?>

<div>
    {{ $this->table }}
</div>