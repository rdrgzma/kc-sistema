<?php

namespace App\Livewire\Planner;

use App\Models\PecaProcessual;
use App\Models\Processo;
use App\Models\Task;
use App\Models\TipoPeca;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Illuminate\View\View;
use Livewire\Component;

class TaskPecaProcessual extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Task $task;

    public function mount(Task $task): void
    {
        $this->task = $task;
    }

    public function registrarPecaAction(): Action
    {
        return Action::make('registrarPeca')
            ->label('Registrar Peça Processual')
            ->icon('heroicon-o-document-plus')
            ->form($this->getFormSchema())
            ->action(function (array $data) {
                // Se a tarefa estiver vinculada a um processo, herda o processo
                if ($this->task->processo_id) {
                    $data['processo_id'] = $this->task->processo_id;
                } elseif ($this->task->taskable_type === Processo::class) {
                    $data['processo_id'] = $this->task->taskable_id;
                } else {
                    $data['processo_id'] = null;
                }

                $data['task_id'] = $this->task->id;

                if (empty($data['autor_id'])) {
                    $data['autor_id'] = auth()->id();
                }

                PecaProcessual::create($data);

                Notification::make()
                    ->title('Peça registrada com sucesso!')
                    ->success()
                    ->send();

                // Recarrega o componente para mostrar os detalhes da peça
                $this->task->refresh();
            });
    }

    public function editarPecaAction(): Action
    {
        return Action::make('editarPeca')
            ->label('Editar')
            ->icon('heroicon-o-pencil')
            ->button()
            ->fillForm(fn () => $this->task->pecaProcessual->toArray())
            ->form($this->getFormSchema())
            ->action(function (array $data) {
                if (empty($data['autor_id'])) {
                    $data['autor_id'] = auth()->id();
                }

                $this->task->pecaProcessual->update($data);

                Notification::make()
                    ->title('Peça atualizada com sucesso!')
                    ->success()
                    ->send();

                $this->task->refresh();
            });
    }

    public function excluirPecaAction(): Action
    {
        return Action::make('excluirPeca')
            ->label('Excluir')
            ->icon('heroicon-o-trash')
            ->button()
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                $this->task->pecaProcessual->delete();

                Notification::make()
                    ->title('Peça excluída.')
                    ->success()
                    ->send();

                $this->task->refresh();
            });
    }

    protected function getFormSchema(): array
    {
        $isGestor = auth()->user()?->hasAnyRole(['Administrador', 'Sócio']);

        return [
            Grid::make(2)
                ->schema([
                    Select::make('tipo_peca_id')
                        ->label('Tipo de Peça')
                        ->options(fn () => TipoPeca::pluck('nome', 'id')->toArray())
                        ->required(),

                    DatePicker::make('data_producao')
                        ->label('Data de Produção')
                        ->required()
                        ->default(now()),

                    Select::make('autor_id')
                        ->label('Autor da Peça')
                        ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->default(auth()->id())
                        ->required()
                        ->visible($isGestor),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.planner.task-peca-processual');
    }
}
