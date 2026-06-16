<?php

namespace App\Http\Controllers;

use App\Models\ApontamentoTempo;
use App\Models\Sentenca;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProdutividadeReportController extends Controller
{
    /**
     * Export decisões (Sentencas) as CSV
     */
    public function exportDecisoes(Request $request): StreamedResponse
    {
        $sentencas = Sentenca::with('processos.pessoa')->get();

        $filename = 'relatorio-decisoes-'.now()->format('d-m-Y').'.csv';

        return response()->streamDownload(function () use ($sentencas) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Processo',
                'Cliente',
                'Tipo de Decisão',
                'Classificação',
                'Valor Economia (R$)',
                'Valor Perda (R$)',
                'Status Financeiro',
            ], ';');

            foreach ($sentencas as $sentenca) {
                $processos = $sentenca->processos->pluck('numero_processo')->implode(', ') ?: '-';
                $clientes = $sentenca->processos
                    ->map(fn ($p) => $p->pessoa?->nome_razao)
                    ->filter()
                    ->unique()
                    ->implode(', ') ?: '-';

                fputcsv($handle, [
                    $processos,
                    $clientes,
                    $sentenca->tipo_decisao ?: '-',
                    $sentenca->classificacao?->getLabel() ?? '-',
                    number_format((float) $sentenca->valor_economia, 2, ',', '.'),
                    number_format((float) $sentenca->valor_perda, 2, ',', '.'),
                    $sentenca->status_financeiro?->getLabel() ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export apontamentos de tempo as CSV
     */
    public function exportApontamentos(Request $request): StreamedResponse
    {
        $apontamentos = ApontamentoTempo::with(['user.equipes', 'processo'])->get();

        $filename = 'relatorio-deslocamento-'.now()->format('d-m-Y').'.csv';

        return response()->streamDownload(function () use ($apontamentos) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Data',
                'Colaborador',
                'Equipe',
                'Atividade',
                'Processo',
                'Local',
                'Modalidade',
                'Início',
                'Fim',
                'Tempo Total (Minutos)',
            ], ';');

            foreach ($apontamentos as $apontamento) {
                fputcsv($handle, [
                    $apontamento->data_atividade?->format('d/m/Y') ?? '-',
                    $apontamento->user?->name ?? '-',
                    $apontamento->user?->equipes?->pluck('nome')?->implode(', ') ?: '-',
                    $apontamento->tipo_atividade?->getLabel() ?? '-',
                    $apontamento->processo?->numero_processo ?? 'Não associado',
                    $apontamento->local ?: '-',
                    $apontamento->modalidade?->getLabel() ?? '-',
                    $apontamento->hora_inicio ?? '-',
                    $apontamento->hora_fim ?? '-',
                    $apontamento->tempo_deslocamento ?? 0,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
