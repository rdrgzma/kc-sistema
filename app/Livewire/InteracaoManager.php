<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\View\View;
use Livewire\Component;

class InteracaoManager extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $model;

    public function mount($model): void
    {
        $this->model = $model;
    }

    public function registrarAction(): Action
    {
        return Action::make('registrar')
            ->label('Novo Atendimento')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('primary')
            ->form([
                Select::make('tipo')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'telefone' => 'Ligação Telefônica',
                        'email' => 'E-mail',
                        'reuniao' => 'Reunião Online',
                        'presencial' => 'Atendimento Presencial',
                    ])
                    ->default('whatsapp')
                    ->required(),
                TextInput::make('assunto')
                    ->required()
                    ->placeholder('Ex: Retorno sobre liminar')
                    ->maxLength(255),
                DateTimePicker::make('data_interacao')
                    ->label('Data/Hora')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->options([
                        'agendada' => 'Agendada (Futuro)',
                        'realizada' => 'Realizada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->default('realizada')
                    ->required(),
                Textarea::make('descricao')
                    ->label('Resumo do Atendimento')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                $this->model->interacoes()->create([
                    'tipo' => $data['tipo'],
                    'assunto' => $data['assunto'],
                    'data_interacao' => $data['data_interacao'],
                    'status' => $data['status'],
                    'descricao' => $data['descricao'],
                    'user_id' => auth()->id(),
                ]);

                $this->dispatch('notify', message: 'Interação registrada com sucesso!');
            })
            ->modalWidth('2xl')
            ->modalHeading('Registrar Novo Atendimento')
            ->modalSubmitActionLabel('Salvar Interação');
    }

    public function render(): View
    {
        return view('livewire.interacao-manager', [
            'interacoes' => $this->model->interacoes()->with('user')->latest('data_interacao')->get(),
        ]);
    }
}
