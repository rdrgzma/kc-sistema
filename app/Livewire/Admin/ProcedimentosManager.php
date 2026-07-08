<?php

namespace App\Livewire\Admin;

use App\Models\Procedimento;
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

class ProcedimentosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Procedimento::query())
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('black'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Procedimento')
                    ->icon('heroicon-o-plus')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->form($this->getFormSchema()),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Procedimento $record) {
                        if ($record->processos()->exists()) {
                            Notification::make()
                                ->title('Não é possível excluir este procedimento')
                                ->body('Existem processos vinculados a este procedimento.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Sem procedimentos cadastrados');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nome')
                ->label('Nome do Procedimento')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: Comum, Sumaríssimo...'),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.procedimentos-manager');
    }
}
