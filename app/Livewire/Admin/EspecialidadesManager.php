<?php

namespace App\Livewire\Admin;

use App\Models\Especialidade;
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

class EspecialidadesManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Especialidade::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Especialidade Médica / Técnica')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('peritos_count')
                    ->label('Peritos')
                    ->counts('peritos')
                    ->badge()
                    ->color('info'),
                TextColumn::make('assistentes_count')
                    ->label('Assistentes')
                    ->counts('assistentes')
                    ->badge()
                    ->color('success'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova Especialidade')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sem especialidades cadastradas')
            ->emptyStateDescription('Cadastre as especialidades para classificar peritos e assistentes.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome da Especialidade')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->placeholder('Ex: Ortopedia, Medicina do Trabalho...'),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.especialidades-manager');
    }
}
