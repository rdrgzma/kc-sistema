<?php

namespace App\Livewire;

use App\Enums\ClassificacaoDecisao;
use App\Enums\ModalidadeAtividade;
use App\Models\ApontamentoTempo;
use App\Models\Sentenca;
use App\Models\Task;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class DashboardProdutividade extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    // Filter properties for period
    public ?string $dataInicio = null;

    public ?string $dataFim = null;

    protected $queryString = [
        'dataInicio' => ['except' => ''],
        'dataFim' => ['except' => ''],
    ];

    public function mount(): void
    {
        // Default to last 30 days
        $this->dataInicio = $this->dataInicio ?? now()->subDays(30)->toDateString();
        $this->dataFim = $this->dataFim ?? now()->toDateString();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['dataInicio', 'dataFim'])) {
            // No need to reset table here anymore, child component is reactive
        }
    }

    /**
     * Get Stats overview data
     */
    public function getStatsProperty(): array
    {
        $start = $this->dataInicio;
        $end = $this->dataFim;

        // 1. Total de Decisões Favoráveis vs Desfavoráveis no período
        $decisoesQuery = Sentenca::query();
        if ($start) {
            $decisoesQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $decisoesQuery->whereDate('created_at', '<=', $end);
        }

        $favoraveisMes = (clone $decisoesQuery)->where('classificacao', ClassificacaoDecisao::FAVORAVEL)->count();
        $desfavoraveisMes = (clone $decisoesQuery)->where('classificacao', ClassificacaoDecisao::DESFAVORAVEL)->count();

        // 2. Soma total do valor_economia (Economia Gerada) no período
        $totalEconomia = (clone $decisoesQuery)->sum('valor_economia');

        // 3. Tempo total de deslocamento da equipe (em horas) no período
        $apontamentosQuery = ApontamentoTempo::select('hora_inicio', 'hora_fim', 'data_atividade')
            ->where('modalidade', ModalidadeAtividade::PRESENCIAL);
        if ($start) {
            $apontamentosQuery->whereDate('data_atividade', '>=', $start);
        }
        if ($end) {
            $apontamentosQuery->whereDate('data_atividade', '<=', $end);
        }
        $totalMinutos = $apontamentosQuery->get()->sum(fn ($apontamento) => $apontamento->tempo_deslocamento);
        $totalHoras = round($totalMinutos / 60, 1);

        // 4. Métricas de Tarefas no período
        $tasksQuery = Task::query();
        if ($start) {
            $tasksQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $tasksQuery->whereDate('created_at', '<=', $end);
        }

        $totalTarefas = (clone $tasksQuery)->count();

        $concluidasQuery = (clone $tasksQuery)->whereHas('bucket', function ($q) {
            $q->where('name', 'like', '%completed%')
                ->orWhere('name', 'like', '%done%')
                ->orWhere('name', 'like', '%conclu%');
        });
        $concluidas = $concluidasQuery->count();
        $somaConclusoes = (clone $tasksQuery)->sum('conclusoes_count');
        $repeticoes = max(0, $somaConclusoes - $concluidas);

        return [
            'favoraveis_mes' => $favoraveisMes,
            'desfavoraveis_mes' => $desfavoraveisMes,
            'total_economia' => $totalEconomia,
            'total_horas_deslocamento' => $totalHoras,
            'total_tarefas' => $totalTarefas,
            'tarefas_concluidas' => $concluidas,
            'tarefas_repeticoes' => $repeticoes,
        ];
    }

    /**
     * Get Chart data: valor_economia vs valor_perda grouped by month
     */
    public function getChartDataProperty(): Collection
    {
        $decisoes = Sentenca::select('valor_economia', 'valor_perda', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        return $decisoes->groupBy(function ($item) {
            return $item->created_at ? $item->created_at->format('M/Y') : '-';
        })->map(function ($group) {
            return [
                'economia' => (float) $group->sum('valor_economia'),
                'perda' => (float) $group->sum('valor_perda'),
            ];
        })->take(-6); // Limit to last 6 active months
    }

    public function render(): View
    {
        return view('livewire.dashboard-produtividade');
    }
}
