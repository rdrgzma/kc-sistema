<?php

namespace App\Livewire\Dashboard;

use App\Enums\ModalidadeAtividade;
use App\Models\ApontamentoTempo;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ProdutividadeDeslocamentosRankingTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Reactive]
    public ?string $dataInicio = null;

    #[Reactive]
    public ?string $dataFim = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select('users.*')
                    ->selectSub(
                        ApontamentoTempo::query()
                            ->selectRaw('COALESCE(SUM((strftime("%H", hora_fim)*60 + strftime("%M", hora_fim)) - (strftime("%H", hora_inicio)*60 + strftime("%M", hora_inicio))), 0)')
                            ->whereColumn('apontamento_tempos.user_id', 'users.id')
                            ->where('modalidade', ModalidadeAtividade::PRESENCIAL)
                            ->when($this->dataInicio, fn ($q) => $q->whereDate('data_atividade', '>=', $this->dataInicio))
                            ->when($this->dataFim, fn ($q) => $q->whereDate('data_atividade', '<=', $this->dataFim)),
                        'tempo_deslocamento_total'
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Membro da Equipe')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tempo_deslocamento_total')
                    ->label('Tempo Deslocamento')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state) {
                        $horas = floor($state / 60);
                        $minutos = $state % 60;

                        return "{$horas}h {$minutos}m";
                    })
                    ->sortable(),
            ])
            ->defaultSort('tempo_deslocamento_total', 'desc')
            ->emptyStateHeading('Sem deslocamento registrado no período selecionado');
    }

    public function render()
    {
        return view('livewire.dashboard.produtividade-deslocamentos-ranking-table');
    }
}
