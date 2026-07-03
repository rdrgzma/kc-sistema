<?php

namespace App\Livewire;

use App\DTOs\PublicacaoDTO;
use App\Enums\TaskUrgency;
use App\Jobs\ProcessarPublicacaoJob;
use App\Models\Bucket;
use App\Models\Task;
use App\Models\TimelineEvent;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Component;

class TimelineFeed extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Model $model;

    public bool $isInitialized = false;

    public function mount(Model $model): void
    {
        $this->model = $model;
        $this->isInitialized = true;
    }

    public function getListeners()
    {
        if (! $this->isInitialized) {
            return [];
        }
        $morphClass = str_replace('\\', '.', $this->model->getMorphClass());

        return [
            "echo:{$morphClass}.{$this->model->id},NovoAndamento" => '$refresh',
        ];
    }

    public function novoAndamentoAction(): Action
    {
        return Action::make('novoAndamento')
            ->label('Novo Andamento')
            ->icon('heroicon-m-plus')
            ->color('blue')
            ->form([
                Select::make('tipo')
                    ->options(['A' => 'Administrativo', 'J' => 'Judicial', 'F' => 'Financeiro'])
                    ->default('A')
                    ->required(),
                DatePicker::make('data_evento')
                    ->default(now())
                    ->required(),
                Textarea::make('descricao')
                    ->label('Descrição do Andamento')
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->model->timelineEvents()->create([
                    'tipo' => $data['tipo'],
                    'descricao' => $data['descricao'],
                    'data_evento' => $data['data_evento'],
                    'user_id' => auth()->id() ?? 1,
                ]);
            });
    }

    public function lancarPublicacaoAction(): Action
    {
        return Action::make('lancarPublicacao')
            ->label('Lançar Publicação Manual')
            ->icon('heroicon-o-document-text')
            ->color('gray')
            ->form([
                Textarea::make('texto_publicacao')
                    ->label('Texto da Publicação')
                    ->required(),
                DatePicker::make('data_publicacao')
                    ->label('Data da Publicação')
                    ->default(now())
                    ->required(),
            ])
            ->action(function (array $data) {
                $dto = new PublicacaoDTO(
                    processoId: $this->model->id,
                    textoPublicacao: $data['texto_publicacao'],
                    dataPublicacao: Carbon::parse($data['data_publicacao'])
                );

                ProcessarPublicacaoJob::dispatchSync($dto);

                Notification::make()
                    ->title('Publicação processada manualmente!')
                    ->success()
                    ->send();
            });
    }

    public function gerarTarefaAction(): Action
    {
        return Action::make('gerarTarefa')
            ->label('Gerar Tarefa')
            ->icon('heroicon-o-briefcase')
            ->color('warning')
            ->form(function (array $arguments) {
                $event = TimelineEvent::find($arguments['id'] ?? null);
                $defaultTitle = $event ? str($event->descricao)->limit(50)->toString() : '';

                return [
                    TextInput::make('title')
                        ->label('Título da Tarefa')
                        ->default($defaultTitle)
                        ->required(),
                    Textarea::make('description')
                        ->label('Descrição (Opcional)')
                        ->default($event ? $event->descricao : ''),
                    Select::make('planner_id')
                        ->label('Quadro (Planner)')
                        ->options(\App\Models\Planner::pluck('name', 'id'))
                        ->live()
                        ->required(),
                    Select::make('bucket_id')
                        ->label('Coluna (Bucket)')
                        ->options(fn($get) => \App\Models\Bucket::where('planner_id', $get('planner_id'))->pluck('name', 'id'))
                        ->required(),
                    DatePicker::make('due_date')
                        ->label('Data de Vencimento')
                        ->required(),
                    Select::make('assigned_to')
                        ->label('Responsável')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('urgency')
                        ->label('Urgência')
                        ->options(TaskUrgency::class)
                        ->default(TaskUrgency::NORMAL)
                        ->required(),
                ];
            })
            ->action(function (array $data) {
                $this->model->tasks()->create([
                    'bucket_id' => $data['bucket_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'assigned_to' => $data['assigned_to'],
                    'due_date' => $data['due_date'],
                    'urgency' => $data['urgency'],
                    'sort' => 0,
                ]);

                Notification::make()
                    ->title('Tarefa criada com sucesso!')
                    ->success()
                    ->send();
            });
    }

    public function with(): array
    {
        if (! $this->isInitialized) {
            return ['events' => collect()];
        }

        return [
            'events' => $this->model->timelineEvents()->latest('data_evento')->get(),
        ];
    }

    public function render(): View
    {
        return view('livewire.timeline-feed');
    }
}
