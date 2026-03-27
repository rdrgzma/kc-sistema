<?php

use App\Models\TimelineEvent;
use App\Models\Processo;
use Livewire\Volt\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;

new class extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Processo $processo;

    public function getListeners()
    {
        return [
            "echo:processos.{$this->processo->id},NovoAndamento" => '$refresh',
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
                $this->processo->timelineEvents()->create([
                    'tipo' => $data['tipo'],
                    'descricao' => $data['descricao'],
                    'data_evento' => $data['data_evento'],
                    'user_id' => auth()->id() ?? 1, // Fallback para dev
                ]);
            });
    }

    public function with(): array
    {
        return [
            'events' => $this->processo->timelineEvents()->latest('data_evento')->get()
        ];
    }
};
?>

<div class="space-y-6">
    <div class="flex justify-between items-center dark:text-gray-200">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 ">Histórico de Andamentos</h3>
        {{ $this->registrarAndamento }}
    </div>

    <div class="relative">
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

        <div class="space-y-8 relative dark:text-gray-200">
            @foreach($events as $event)
                <div class="flex items-start gap-4">
                    <div class="z-10 w-8 h-8 rounded-full flex items-center justify-center shadow-sm 
                        {{ $event->tipo === 'J' ? 'bg-blue-600' : ($event->tipo === 'F' ? 'bg-green-600' : 'bg-gray-500') }}">
                        <span class="text-white text-xs font-bold">{{ $event->tipo }}</span>
                    </div>

                    <div class="flex-1 bg-white p-4 rounded-lg border border-gray-100 shadow-sm dark:bg-zinc-900 dark:border-zinc-800">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-medium text-gray-500 italic dark:text-gray-200 dark:border-gray-700">
                                {{ \Carbon\Carbon::parse($event->data_evento)->format('d/m/Y H:i') }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-200 dark:border-gray-700">Por: {{ $event->user->name ?? 'Sistema' }}</span>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed dark:text-gray-200 dark:border-gray-700">{{ $event->descricao }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <x-filament-actions::modals />
</div>