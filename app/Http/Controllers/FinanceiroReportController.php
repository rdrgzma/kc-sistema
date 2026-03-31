<?php

namespace App\Http\Controllers;

use App\Models\LancamentoFinanceiro;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceiroReportController extends Controller
{
    public function export(Request $request)
    {
        $filters = $request->input('filters', []);
        $search = $request->input('search', '');

        $query = LancamentoFinanceiro::query()
            ->estratificado()
            ->with(['categoria', 'lancamentable']);

        // Aplicando filtros inteligentes do Filament manualmente no Controlador
        if ($search) {
            $query->where('descricao', 'like', "%{$search}%");
        }

        if (collect($filters['tipo'] ?? null)->isNotEmpty()) {
            $value = $filters['tipo']['value'] ?? null;
            if ($value) {
                $query->where('tipo', $value);
            }
        }

        if (collect($filters['status'] ?? null)->isNotEmpty()) {
            $value = $filters['status']['value'] ?? null;
            if ($value) {
                $query->where('status', $value);
            }
        }

        if (collect($filters['categoria_financeira_id'] ?? null)->isNotEmpty()) {
            $value = $filters['categoria_financeira_id']['value'] ?? null;
            if ($value) {
                $query->where('categoria_financeira_id', $value);
            }
        }

        $dateRange = $filters['data_vencimento'] ?? null;
        if ($dateRange) {
            if ($dateRange['desde'] ?? null) {
                $query->whereDate('data_vencimento', '>=', $dateRange['desde']);
            }
            if ($dateRange['ate'] ?? null) {
                $query->whereDate('data_vencimento', '<=', $dateRange['ate']);
            }
        }

        $lancamentos = $query->latest('data_vencimento')->get();

        $pdf = Pdf::loadView('pdf.financial-report', [
            'lancamentos' => $lancamentos,
            'periodo' => [
                'desde' => $dateRange['desde'] ?? 'Início',
                'ate' => $dateRange['ate'] ?? 'Fim',
            ],
            'resumo' => [
                'total_receita' => $lancamentos->where('tipo', 'receita')->sum('valor'),
                'total_despesa' => $lancamentos->where('tipo', 'despesa')->sum('valor'),
            ],
        ]);

        return $pdf->download('relatorio-financeiro-'.now()->format('d-m-Y').'.pdf');
    }
}
