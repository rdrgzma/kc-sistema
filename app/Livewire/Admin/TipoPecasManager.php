<?php

namespace App\Livewire\Admin;

use App\Models\TipoPeca;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class TipoPecasManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(TipoPeca::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Tipo de Peça')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, TipoPeca $record) {
                        if ($record->pecasProcessuais()->exists()) {
                            Notification::make()
                                ->title('Não é possível excluir este tipo de peça')
                                ->body('Existem peças processuais cadastradas com este tipo.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Sem tipos de peça cadastrados');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome do Tipo de Peça')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: Contestação, Recurso...'),
            TextInput::make('descricao')
                ->label('Descrição')
                ->maxLength(255)
                ->placeholder('Ex: Peça para contestar a ação...'),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.tipo-pecas-manager');
    }
}
