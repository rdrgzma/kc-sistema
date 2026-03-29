<?php

namespace App\Livewire;

use App\Enums\DurationUnit;
use App\Enums\TaskUrgency;
use App\Models\Planner;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskDurationService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class PlannerBoard extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    #[Url(as: 'p')]
    public ?int $selectedPlannerId = null;

    // Agora é uma Collection de Planners ou um único Planner
    public $plannersData;

    public function mount()
    {
        $this->loadPlanners();
    }

    /**
     * Centralizamos o carregamento para poder ser chamado após criar/editar tarefas
     */
    public function loadPlanners()
    {
        if ($this->selectedPlannerId) {
            $this->plannersData = Planner::with(['buckets.tasks.assignee', 'plannable'])
                ->find($this->selectedPlannerId);
            
            // Se não encontrar, volta para o index
            if (!$this->plannersData) {
                $this->selectedPlannerId = null;
                $this->loadPlanners();
            }
            return;
        }

        // Traz os Planners criados pelo utilizador OU onde ele tem tarefas atribuídas
        $this->plannersData = Planner::withCount('tasks')
            ->with(['user', 'plannable'])
            ->where('user_id', auth()->id())
            ->orWhereHas('tasks', function ($query) {
                $query->where('assigned_to', auth()->id());
            })
            ->latest()
            ->get();
    }

    public function selectPlanner(int $id)
    {
        $this->selectedPlannerId = $id;
        $this->loadPlanners();
    }

    public function backToIndex()
    {
        $this->selectedPlannerId = null;
        $this->loadPlanners();
    }

    /**
     * Atualiza a ordem quando um card é arrastado (Drag & Drop).
     * Funciona globalmente pois cada $task['value'] (ID da tarefa) é único.
     */
    public function updateTaskOrder($tasks)
    {
        foreach ($tasks as $item) {
            Task::where('id', $item['value'])->update([
                'sort' => $item['order'],
                'bucket_id' => $item['bucket_id'],
            ]);
        }

        $this->loadPlanners();
    }

    public function createPlannerAction(): Action
    {
        return Action::make('createPlanner')
            ->label('Novo Quadro')
            ->icon('heroicon-o-layout-grid')
            ->modalWidth('md')
            ->form([
                TextInput::make('name')
                    ->label('Nome do Quadro')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255),
                Repeater::make('buckets')
                    ->label('Colunas (Fases)')
                    ->schema([
                        TextInput::make('name')->label('Nome')->required(),
                        ColorPicker::make('color')->label('Cor')->required()
                    ])
                    ->defaultItems(3)
                    ->default([
                        ['name' => 'A Fazer', 'color' => '#64748b'],
                        ['name' => 'Em Andamento', 'color' => '#3b82f6'],
                        ['name' => 'Concluído', 'color' => '#10b981'],
                    ])
                    ->reorderableWithDragAndDrop()
            ])
            ->action(function (array $data) {
                $plannerData = [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'user_id' => auth()->id(),
                ];
                $planner = Planner::create($plannerData);

                // Cria as colunas definidas no repeater
                $buckets = collect($data['buckets'] ?? [])->map(function ($bucket, $index) {
                    return [
                        'name' => $bucket['name'],
                        'color' => $bucket['color'],
                        'sort' => $index + 1,
                    ];
                });

                if ($buckets->isNotEmpty()) {
                    $planner->buckets()->createMany($buckets->toArray());
                }

                $this->loadPlanners();
            });
    }

    public function createBucketAction(): Action
    {
        return Action::make('createBucket')
            ->label('Nova Coluna')
            ->modalWidth('sm')
            ->form([
                TextInput::make('name')
                    ->label('Nome da Coluna')
                    ->required(),
                ColorPicker::make('color')
                    ->label('Cor')
                    ->default('#64748b')
            ])
            ->action(function (array $data, array $arguments) {
                \App\Models\Bucket::create([
                    'planner_id' => $arguments['planner_id'],
                    'name' => $data['name'],
                    'color' => $data['color'],
                    'sort' => 999
                ]);
                $this->loadPlanners();
            });
    }

    public function editBucketAction(): Action
    {
        return Action::make('editBucket')
            ->label('Editar Coluna')
            ->modalWidth('sm')
            ->fillForm(fn (array $arguments) => \App\Models\Bucket::find($arguments['bucket_id'])->toArray())
            ->form([
                TextInput::make('name')
                    ->label('Nome da Coluna')
                    ->required(),
                ColorPicker::make('color')
                    ->label('Cor')
            ])
            ->action(function (array $data, array $arguments) {
                $bucket = \App\Models\Bucket::find($arguments['bucket_id']);
                if ($bucket) {
                    $bucket->update([
                        'name' => $data['name'],
                        'color' => $data['color']
                    ]);
                }
                $this->loadPlanners();
            });
    }

    public function createTaskAction(): Action
    {
        return Action::make('createTask')
            ->label('Nova Tarefa')
            ->icon('heroicon-o-plus')
            ->modalWidth('2xl')
            ->form($this->getTaskFormSchema())
            ->action(function (array $data, array $arguments) {
                // O $arguments['bucket_id'] vem do botão específico onde o user clicou
                Task::create(array_merge($data, [
                    'bucket_id' => $arguments['bucket_id'],
                    'sort' => 999, // Vai para o fim
                ]));

                $this->loadPlanners();
            });
    }

    public function editTaskAction(): Action
    {
        return Action::make('editTask')
            ->modalWidth('5xl') // Alargamos o modal para caber bem os documentos e a timeline
            ->modalHeading(fn (array $arguments) => 'Tarefa: '.Task::find($arguments['task_id'])->title)
            ->fillForm(fn (array $arguments) => Task::find($arguments['task_id'])->toArray())

            ->form(function (array $arguments) {
                // Carregamos a tarefa para saber as contagens e passá-la para as views
                $task = Task::find($arguments['task_id']);

                return [
                    Tabs::make('TaskTabs')
                        ->tabs([
                            // ABA 1: O seu formulário original
                            Tab::make('Detalhes')
                                ->icon('heroicon-o-information-circle')
                                ->schema($this->getTaskFormSchema()),

                            // ABA 2: Comentários
                            Tab::make('Comentários')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->badge($task->comentarios()->count())
                                ->schema([
                                    View::make('livewire.planner.partials.tab-comentarios')
                                        ->viewData(['task' => $task]),
                                ]),

                            // ABA 3: Reutilizando o seu DocumentManager
                            Tab::make('Documentos')
                                ->icon('heroicon-o-paper-clip')
                                ->badge($task->documentos()->count())
                                ->schema([
                                    View::make('livewire.planner.partials.tab-documentos')
                                        ->viewData(['task' => $task]),
                                ]),

                            // ABA 4: Reutilizando o seu TimelineFeed
                            Tab::make('Histórico')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    View::make('livewire.planner.partials.tab-timeline')
                                        ->viewData(['task' => $task]),
                                ]),
                        ])
                        ->activeTab(1) // Abre sempre nos Detalhes
                        ->contained(false), // Remove a borda excessiva das tabs do Filament
                ];
            })
            ->action(function (array $data, array $arguments) {
                // Esta action só lida com a Aba 1 (Detalhes)
                // As outras abas gravam-se a si próprias via os seus próprios componentes Livewire
                $task = Task::find($arguments['task_id']);
                if ($task) {
                    $task->update($data);
                }

                $this->loadPlanners();
            });
    }


    /**
     * O esquema "mágico" do formulário, reaproveitado para Criar e Editar.
     */
    protected function getTaskFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Título da Tarefa')
                ->required()
                ->maxLength(255),

            Select::make('urgency')
                ->label('Urgência')
                ->options(TaskUrgency::class)
                ->default(TaskUrgency::NORMAL->value)
                ->required(),

            Select::make('assigned_to')
                ->label('Responsável')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->nullable(),

            RichEditor::make('description')
                ->label('Descrição')
                ->columnSpanFull(),

            // A Mágica do Tempo que criámos
            Grid::make(3)->schema([
                DateTimePicker::make('due_date')
                    ->label('Prazo Final')
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) {
                            $set('duration_value', null);

                            return;
                        }
                        $duration = app(TaskDurationService::class)->calculateDuration(Carbon::parse($state));
                        $set('duration_value', $duration['value']);
                        $set('duration_unit', $duration['unit']->value);
                    }),

                TextInput::make('duration_value')
                    ->label('Tempo Estimado')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        if (! $state || ! $get('duration_unit')) {
                            return;
                        }
                        $unit = $get('duration_unit');
                        $unitEnum = $unit instanceof DurationUnit ? $unit : DurationUnit::from($unit);
                        $dueDate = app(TaskDurationService::class)->calculateDueDate(
                            (int) $state, $unitEnum
                        );
                        $set('due_date', $dueDate->toDateTimeString());
                    }),

                Select::make('duration_unit')
                    ->label('Unidade')
                    ->options(DurationUnit::class)
                    ->default(DurationUnit::DIAS->value)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        if (! $state || ! $get('duration_value')) {
                            return;
                        }
                        $unitEnum = $state instanceof DurationUnit ? $state : DurationUnit::from($state);
                        $dueDate = app(TaskDurationService::class)->calculateDueDate(
                            (int) $get('duration_value'), $unitEnum
                        );
                        $set('due_date', $dueDate->toDateTimeString());
                    }),
            ]),
        ];
    }

    public function updateTaskBucket($taskId, $bucketId)
    {
        $task = Task::find($taskId);
        if ($task && $task->bucket_id !== $bucketId) {
            $task->update(['bucket_id' => $bucketId]);
            $this->loadPlanners();
        }
    }

    public function render()
    {
        return view('livewire.planner-board');
    }
}
