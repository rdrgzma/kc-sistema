<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Fase;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Seguradora;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;

class OnboardingWizard extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

    /** ID da Pessoa já existente encontrada pelo CPF/CNPJ. Null se for novo cadastro. */
    public ?int $pessoaExistenteId = null;

    public function mount(): void
    {
        $this->form->fill([
            'tipo' => 'PF',
            'tipo_evento' => 'J',
            'data_evento' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Cliente')
                        ->description('Identificação básica')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('tipo')
                                        ->options([
                                            'PF' => 'Pessoa Física',
                                            'PJ' => 'Pessoa Jurídica',
                                        ])
                                        ->required()
                                        ->live()
                                        ->disabled(fn () => $this->pessoaExistenteId !== null),

                                    TextInput::make('cpf_cnpj')
                                        ->label(fn ($get) => $get('tipo') === 'PJ' ? 'CNPJ' : 'CPF')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Set $set, Component $livewire) {
                                            $livewire->verificarCliente($state, $set);
                                        }),

                                    TextInput::make('nome_razao')
                                        ->label('Nome / Razão Social')
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled(fn () => $this->pessoaExistenteId !== null),

                                    TextInput::make('email')
                                        ->email()
                                        ->disabled(fn () => $this->pessoaExistenteId !== null),

                                    TextInput::make('telefone')
                                        ->tel()
                                        ->disabled(fn () => $this->pessoaExistenteId !== null),
                                ]),
                        ]),

                    Step::make('Processo')
                        ->description('Abertura do caso')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('numero_processo')
                                ->label('Nº do Processo')
                                ->unique('processos', 'numero_processo')
                                ->placeholder('Ex: 0000000-00.0000.0.00.0000'),

                            Grid::make(2)
                                ->schema([
                                    Select::make('area_id')
                                        ->label('Área Jurídica')
                                        ->options(fn () => Area::orderBy('nome')->pluck('nome', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Select::make('fase_id')
                                        ->label('Fase Atual')
                                        ->options(fn () => Fase::orderBy('nome')->pluck('nome', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Select::make('procedimento_id')
                                        ->label('Procedimento')
                                        ->options(fn () => Procedimento::orderBy('nome')->pluck('nome', 'id'))
                                        ->searchable(),
                                    Select::make('seguradora_id')
                                        ->label('Seguradora (Opcional)')
                                        ->options(fn () => Seguradora::orderBy('nome')->pluck('nome', 'id'))
                                        ->searchable(),
                                ]),
                        ]),

                    Step::make('Timeline')
                        ->description('Primeira ação registrada')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Select::make('tipo_evento')
                                ->label('Tipo de Andamento')
                                ->options([
                                    'J' => 'Jurídico',
                                    'A' => 'Administrativo',
                                    'F' => 'Financeiro',
                                ])
                                ->required(),
                            Textarea::make('descricao_evento')
                                ->label('Resumo do Primeiro Andamento')
                                ->required()
                                ->placeholder('Ex: Recebimento do cliente e conferência documental.'),
                            DateTimePicker::make('data_evento')
                                ->label('Data do Evento')
                                ->required(),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-8 py-2 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 dark:shadow-none">Finalizar Onboarding</button>')),
            ]);
    }

    /**
     * Verifica se já existe um cliente com o CPF/CNPJ informado.
     * Se sim: preenche os dados via Set e exibe banner.
     */
    public function verificarCliente(?string $cpfCnpj, Set $set): void
    {
        $cpfCnpjLivre = preg_replace('/[^0-9]/', '', (string) $cpfCnpj);

        if (strlen($cpfCnpjLivre) < 11) {
            $this->pessoaExistenteId = null;

            return;
        }

        $pessoa = Pessoa::where('cpf_cnpj', 'like', "%{$cpfCnpjLivre}%")->first();

        if ($pessoa) {
            $this->pessoaExistenteId = $pessoa->id;

            // Preenche os campos de forma seletiva
            $set('tipo', $pessoa->tipo);
            $set('nome_razao', $pessoa->nome_razao);
            $set('cpf_cnpj', $pessoa->cpf_cnpj);
            $set('email', $pessoa->email);
            $set('telefone', $pessoa->telefone);

            $this->dispatch('notify', message: "✅ Cliente já cadastrado: {$pessoa->nome_razao}. Dados carregados — novo processo será vinculado.");
        } else {
            $this->pessoaExistenteId = null;
        }
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            // Reutiliza pessoa existente ou cria nova
            $pessoa = $this->pessoaExistenteId
                ? Pessoa::findOrFail($this->pessoaExistenteId)
                : Pessoa::create([
                    'tipo' => $data['tipo'],
                    'nome_razao' => $data['nome_razao'],
                    'cpf_cnpj' => $data['cpf_cnpj'],
                    'email' => $data['email'] ?? null,
                    'telefone' => $data['telefone'] ?? null,
                ]);

            $processo = $pessoa->processos()->create([
                'numero_processo' => $data['numero_processo'],
                'area_id' => $data['area_id'],
                'fase_id' => $data['fase_id'],
                'procedimento_id' => $data['procedimento_id'] ?? null,
                'seguradora_id' => $data['seguradora_id'] ?? null,
            ]);

            $processo->timelineEvents()->create([
                'tipo' => $data['tipo_evento'],
                'descricao' => $data['descricao_evento'],
                'data_evento' => $data['data_evento'],
                'user_id' => auth()->id(),
            ]);
        });

        $msg = $this->pessoaExistenteId
            ? 'Novo processo vinculado ao cliente existente com sucesso!'
            : 'Fluxo concluído! Cliente e Processo ativados.';

        $this->dispatch('notify', message: $msg);
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.onboarding-wizard');
    }
}
