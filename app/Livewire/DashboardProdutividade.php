<?php

namespace App\Livewire;

use App\Enums\ClassificacaoDecisao;
use App\Models\ApontamentoTempo;
use App\Models\Sentenca;
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
        // 1. Total de Decisões Favoráveis vs Desfavoráveis no mês atual
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $favoraveisMes = Sentenca::where('classificacao', ClassificacaoDecisao::FAVORAVEL)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $desfavoraveisMes = Sentenca::where('classificacao', ClassificacaoDecisao::DESFAVORAVEL)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // 2. Soma total do valor_economia (Economia Gerada)
        $totalEconomia = Sentenca::sum('valor_economia');

        // 3. Tempo total de deslocamento da equipe (em horas)
        $apontamentos = ApontamentoTempo::select('hora_inicio', 'hora_fim')->get();
        $totalMinutos = $apontamentos->sum(fn ($apontamento) => $apontamento->tempo_deslocamento);
        $totalHoras = round($totalMinutos / 60, 1);

        return [
            'favoraveis_mes' => $favoraveisMes,
            'desfavoraveis_mes' => $desfavoraveisMes,
            'total_economia' => $totalEconomia,
            'total_horas_deslocamento' => $totalHoras,
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
