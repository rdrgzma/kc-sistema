<?php

namespace App\Livewire;

use App\Models\TimelineEvent;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Illuminate\View\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;

class TimelineFeed extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Model $model;
    public bool $isInitialized = false;

    public function mount(Model $model): void
    {
        $this->model = $model;
        $this->isInitialized = true;
    }

    public function getListeners()
    {
        if (!$this->isInitialized) {
            return [];
        }
        $morphClass = str_replace('\\', '.', $this->model->getMorphClass());
        return [
            "echo:{$morphClass}.{$this->model->id},NovoAndamento" => '$refresh',
        ];
    }

    public function registrarAndamentoAction(): Action
    {
        return Action::make('registrarAndamento')
            ->label('Novo Andamento')
            ->icon('heroicon-m-plus')
            ->color('blue')
            ->form([
                Select::make('tipo')
                    ->options(['A' => 'Administrativo', 'J' => 'Judicial', 'F' => 'Financeiro'])
                    ->default('A')
                    ->required(),
                DateTimePicker::make('data_evento')
                    ->default(now())
                    ->required(),
                Textarea::make('descricao')
                    ->label('Descrição do Andamento')
                    ->required(),
            ])
            ->action(function (array $data) {
                // Tenta achar a relacao correta timelineEvents se existir.
                $this->model->timelineEvents()->create([
                    'tipo' => $data['tipo'],
                    'descricao' => $data['descricao'],
                    'data_evento' => $data['data_evento'],
                    'user_id' => auth()->id() ?? 1, // Fallback para dev
                ]);
            });
    }

    public function with(): array
    {
        if (!$this->isInitialized) {
            return ['events' => collect()];
        }

        return [
            'events' => $this->model->timelineEvents()->latest('data_evento')->get()
        ];
    }


    public function render(): View
    {
        return view('livewire.timeline-feed');
    }
}
