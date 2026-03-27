<?php

namespace App\Livewire;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Livewire\Component;

class InteracaoManager extends Component implements HasForms
{
    use InteractsWithForms;

    public $model;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount($model): void
    {
        $this->model = $model;

        $this->form->fill([
            'tipo' => 'whatsapp',
            'status' => 'realizada',
            'data_interacao' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Select::make('tipo')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'telefone' => 'Ligação Telefônica',
                        'email' => 'E-mail',
                        'reuniao' => 'Reunião Online',
                        'presencial' => 'Atendimento Presencial',
                    ])
                    ->required(),

                TextInput::make('assunto')
                    ->required()
                    ->placeholder('Ex: Retorno sobre liminar')
                    ->maxLength(255),

                DateTimePicker::make('data_interacao')
                    ->label('Data/Hora')
                    ->required(),

                Select::make('status')
                    ->options([
                        'agendada' => 'Agendada (Futuro)',
                        'realizada' => 'Realizada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->required(),

                Textarea::make('descricao')
                    ->label('Resumo do Atendimento')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function registrar(): void
    {
        $data = $this->form->getState();

        $this->model->interacoes()->create([
            'tipo' => $data['tipo'],
            'assunto' => $data['assunto'],
            'data_interacao' => $data['data_interacao'],
            'status' => $data['status'],
            'descricao' => $data['descricao'],
            'user_id' => auth()->id(),
        ]);

        $this->form->fill([
            'tipo' => 'whatsapp',
            'status' => 'realizada',
            'data_interacao' => now()->format('Y-m-d H:i:s'),
            'assunto' => null,
            'descricao' => null,
        ]);

        $this->dispatch('notify', message: 'Interação registrada com sucesso!');
    }

    public function render(): View
    {
        return view('livewire.interacao-manager', [
            'interacoes' => $this->model->interacoes()->with('user')->latest('data_interacao')->get(),
        ]);
    }
}
