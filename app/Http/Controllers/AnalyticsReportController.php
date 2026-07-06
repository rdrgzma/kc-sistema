<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Processo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsReportController extends Controller
{
    protected function getFilteredQuery(Request $request)
    {
        $filters = $request->input('filters', []);
        $search = $request->input('search', '');

        $query = Activity::query()
            ->with(['causer', 'subject']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (collect($filters['causer_id'] ?? null)->isNotEmpty()) {
            $value = $filters['causer_id']['value'] ?? null;
            if ($value) {
                $query->where('causer_id', $value);
            }
        }

        if (collect($filters['subject_type'] ?? null)->isNotEmpty()) {
            $value = $filters['subject_type']['value'] ?? null;
            if ($value) {
                $query->where('subject_type', $value);
            }
        }

        $dateRange = $filters['created_at'] ?? null;
        if ($dateRange) {
            if ($dateRange['data_de'] ?? null) {
                $query->where('created_at', '>=', $dateRange['data_de']);
            }
            if ($dateRange['data_ate'] ?? null) {
                $query->where('created_at', '<=', $dateRange['data_ate']);
            }
        }

        return $query->latest();
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $activities = $this->getFilteredQuery($request)->get();
        $filename = 'relatorio-analytics-'.now()->format('d-m-Y').'.csv';

        return response()->streamDownload(function () use ($activities) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Data/Hora',
                'Operador',
                'Ação / Descrição',
                'Registro Alvo',
            ], ';');

            foreach ($activities as $act) {
                $target = 'Não disponível';
                if ($act->subject) {
                    $class = class_basename($act->subject_type);
                    if ($act->subject_type === Pessoa::class) {
                        $name = $act->subject->nome_razao;
                    } elseif ($act->subject_type === Processo::class) {
                        $name = $act->subject->numero_processo;
                    } else {
                        $name = $act->subject->nome ?? $act->subject->title ?? "#{$act->subject_id}";
                    }
                    $target = "{$class}: {$name}";
                } elseif ($act->subject_type) {
                    $target = class_basename($act->subject_type).' #'.$act->subject_id;
                }

                fputcsv($handle, [
                    $act->created_at?->format('d/m/Y H:i:s') ?? '-',
                    $act->causer?->name ?? 'Sistema',
                    $act->description ?? '-',
                    $target,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $activities = $this->getFilteredQuery($request)->get();

        $pdf = Pdf::loadView('pdf.analytics-report', [
            'activities' => $activities,
        ]);

        return $pdf->download('relatorio-analytics-'.now()->format('d-m-Y').'.pdf');
    }
}
