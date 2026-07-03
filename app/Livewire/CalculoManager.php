<?php

namespace App\Livewire;

use App\Domain\Calculos\AtualizadorMonetario;
use App\Domain\Calculos\CalculadoraJudicial;
use App\Domain\Calculos\CalculadorJuros;
use App\Domain\Calculos\DTOs\ParcelaDTO;
use App\Models\Calculo;
use App\Models\Indexador;
use App\Models\IndexadorCotacao;
use App\Models\Processo;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeImmutable;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cálculos')]
class CalculoManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Processo $processo = null;

    public ?array $data = [];

    public function mount(?Processo $processo = null): void
    {
        $this->processo = $processo;
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('processo_id')
                ->label('Vincular a um Processo (Opcional)')
                ->options(function () {
                    return \App\Models\Processo::all()->mapWithKeys(function ($p) {
                        return [$p->id => $p->numero_processo ? "{$p->numero_processo} (ID: {$p->id})" : "Processo #{$p->id}"];
                    });
                })
                ->searchable()
                ->hidden(fn () => $this->processo !== null)
                ->columnSpanFull(),
            TextInput::make('titulo')
                ->label('Título do Cálculo')
                ->required()
                ->columnSpanFull(),
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Parâmetros')
                        ->schema([
                            DatePicker::make('data_atualizacao')
                                ->label('Data de Atualização')
                                ->required()
                                ->default(now()),
                            Select::make('indexador_id')
                                ->label('Índice de Correção')
                                ->required()
                                ->options(
                                    Indexador::all()->groupBy('categoria')->map(fn ($group) => $group->pluck('nome', 'id'))
                                )
                                ->searchable(),
                            Toggle::make('deflacionar_negativo')
                                ->label('Deflacionar em caso de índice negativo?')
                                ->default(false),
                        ]),
                    Tabs\Tab::make('Juros')
                        ->schema([
                            Fieldset::make('Configurações de Juros')
                                ->schema([
                                    Radio::make('tipo')
                                        ->label('Tipo de Juros')
                                        ->options([
                                            'percentual' => 'Percentual Fixo',
                                            'taxa_legal' => 'Taxa Legal (Lei 14.905)',
                                            'selic' => 'SELIC',
                                        ])
                                        ->default('percentual'),
                                    Radio::make('periodo')
                                        ->label('Periodicidade')
                                        ->options([
                                            'mensal' => 'Mensal',
                                            'anual' => 'Anual',
                                            'diario' => 'Diário',
                                        ])
                                        ->default('mensal'),
                                    Toggle::make('pro_rata')
                                        ->label('Calcular Pró-rata?')
                                        ->default(true),
                                ]),
                        ]),
                    Tabs\Tab::make('Parcelas')
                        ->schema([
                            Repeater::make('parcelas')
                                ->schema([
                                    DatePicker::make('data_vencimento')
                                        ->label('Data de Vencimento')
                                        ->required(),
                                    TextInput::make('valor')
                                        ->label('Valor (R$)')
                                        ->numeric()
                                        ->required(),
                                    Select::make('tipo')
                                        ->label('Tipo')
                                        ->options([
                                            'debito' => 'Débito Principal',
                                            'custa' => 'Custas Judiciais',
                                            'desconto' => 'Desconto / Abatimento',
                                        ])
                                        ->required(),
                                    TextInput::make('descricao')
                                        ->label('Descrição')
                                        ->maxLength(255),
                                ])
                                ->columns(4)
                                ->defaultItems(1),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $calculo = Calculo::create([
            'processo_id' => $this->processo?->id ?? ($state['processo_id'] ?? null),
            'titulo' => $state['titulo'],
            'data_atualizacao' => $state['data_atualizacao'],
            'indexador_id' => $state['indexador_id'],
            'parametros' => [
                'deflacionar_negativo' => $state['deflacionar_negativo'] ?? false,
                'juros' => [
                    'tipo' => $state['tipo'] ?? 'percentual',
                    'periodo' => $state['periodo'] ?? 'mensal',
                    'pro_rata' => $state['pro_rata'] ?? true,
                ],
                'parcelas' => $state['parcelas'] ?? [],
            ],
            'valor_original' => 0,
            'valor_corrigido' => 0,
            'juros_total' => 0,
            'valor_final' => 0,
        ]);

        $this->calcularTotaisEAtualizar($calculo, $state);

        Notification::make()
            ->title('Cálculo salvo com sucesso')
            ->success()
            ->send();

        $this->form->fill();
    }

    private function calcularTotaisEAtualizar(Calculo $calculo, array $state): void
    {
        $atualizador = new AtualizadorMonetario;

        $cotacoes = IndexadorCotacao::where('indexador_id', $calculo->indexador_id)
            ->orderBy('data_referencia', 'asc')
            ->pluck('valor', 'data_referencia')
            ->toArray();

        $atualizador->setFatores($cotacoes);

        $calculadorJuros = new CalculadorJuros;
        $calculadora = new CalculadoraJudicial($atualizador, $calculadorJuros);

        $parcelas = array_map(function ($p) {
            return new ParcelaDTO(
                new DateTimeImmutable($p['data_vencimento']),
                (float) $p['valor'],
                $p['tipo'] ?? 'principal'
            );
        }, $state['parcelas'] ?? []);

        $tipoJuros = $state['tipo'] ?? 'percentual';
        $taxaMensal = ($tipoJuros === 'selic') ? 0.0 : 1.0; // Se for SELIC, a taxa de juros a mais é 0
        $jurosCompostos = false;

        $dataAtualizacao = new DateTimeImmutable($state['data_atualizacao']);

        $memorial = $calculadora->calcularMemorial(
            $parcelas,
            $dataAtualizacao,
            $taxaMensal,
            $jurosCompostos
        );

        $valorOriginal = 0;
        $valorCorrigido = 0;
        $jurosTotal = 0;
        $valorFinal = 0;
        $linhasArray = [];

        foreach ($memorial as $linha) {
            $valorOriginal += $linha->valorOriginal;
            $valorCorrigido += $linha->valorCorrigido;
            $jurosTotal += $linha->juros;
            $valorFinal += $linha->valorFinal;

            $linhasArray[] = [
                'data' => $linha->data->format('Y-m-d'),
                'valor_original' => $linha->valorOriginal,
                'fator' => $linha->fator,
                'valor_corrigido' => $linha->valorCorrigido,
                'dias' => $linha->dias,
                'juros' => $linha->juros,
                'valor_final' => $linha->valorFinal,
            ];
        }

        $parametros = $calculo->parametros;
        $parametros['memorial'] = $linhasArray;

        $calculo->update([
            'parametros' => $parametros,
            'valor_original' => $valorOriginal,
            'valor_corrigido' => $valorCorrigido,
            'juros_total' => $jurosTotal,
            'valor_final' => $valorFinal,
        ]);
    }

    public function gerarPdf(Calculo $calculo)
    {
        $pdf = Pdf::loadView('pdf.memorial-calculo', ['calculo' => $calculo]);

        return response()->streamDownload(fn () => print ($pdf->output()), "calculo-{$calculo->id}.pdf");
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Calculo::query()
                    ->when($this->processo, fn ($query) => $query->where('processo_id', $this->processo->id))
            )
            ->columns([
                TextColumn::make('processo.numero_processo')
                    ->label('Processo')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn () => $this->processo !== null),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('data_atualizacao')
                    ->label('Atualizado em')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('valor_final')
                    ->label('Valor Final')
                    ->money('BRL')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('verificar_drcalc')
                    ->label('Verificar no DrCalc')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url('https://www.drcalc.net')
                    ->openUrlInNewTab()
                    ->color('info'),
            ])
            ->actions([
                Action::make('gerar_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn (Calculo $record) => $this->gerarPdf($record)),
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['deflacionar_negativo'] = $data['parametros']['deflacionar_negativo'] ?? false;
                        $data['tipo'] = $data['parametros']['juros']['tipo'] ?? 'percentual';
                        $data['periodo'] = $data['parametros']['juros']['periodo'] ?? 'mensal';
                        $data['pro_rata'] = $data['parametros']['juros']['pro_rata'] ?? true;
                        $data['parcelas'] = $data['parametros']['parcelas'] ?? [];

                        return $data;
                    })
                    ->using(function (Calculo $record, array $data): Calculo {
                        $record->update([
                            'processo_id' => $data['processo_id'] ?? $record->processo_id,
                            'titulo' => $data['titulo'],
                            'data_atualizacao' => $data['data_atualizacao'],
                            'indexador_id' => $data['indexador_id'],
                            'parametros' => array_merge($record->parametros ?? [], [
                                'deflacionar_negativo' => $data['deflacionar_negativo'] ?? false,
                                'juros' => [
                                    'tipo' => $data['tipo'] ?? 'percentual',
                                    'periodo' => $data['periodo'] ?? 'mensal',
                                    'pro_rata' => $data['pro_rata'] ?? true,
                                ],
                                'parcelas' => $data['parcelas'] ?? [],
                            ]),
                        ]);

                        $this->calcularTotaisEAtualizar($record, $data);

                        return $record;
                    }),
                DeleteAction::make(),
            ]);
    }

    public function render()
    {
        return view('livewire.calculo-manager');
    }
}
