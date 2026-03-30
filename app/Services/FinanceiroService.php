<?php

namespace App\Services;

use App\Models\LancamentoFinanceiro;
use App\Models\Processo;
use App\Models\RateioHonorario;
use Illuminate\Support\Facades\DB;

class FinanceiroService
{
    /**
     * Gera uma custa automática atrelada a um processo.
     */
    public function gerarCustaAutomatica(
        Processo $processo,
        string $descricao,
        float $valor,
        ?string $dataVencimento = null
    ): LancamentoFinanceiro {
        return $processo->lancamentosFinanceiros()->create([
            'descricao' => $descricao,
            'valor' => $valor,
            'data_vencimento' => $dataVencimento,
            'tipo' => 'despesa',
            'status' => 'pendente',
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Calcula o rateio de sucesso e cria os registros individuais.
     *
     * @param  array<int>  $advogadosIds
     */
    public function calcularRateioExito(
        LancamentoFinanceiro $lancamento,
        array $advogadosIds,
        float $percentualTotalDaCausa
    ): void {
        if (empty($advogadosIds)) {
            return;
        }

        // Calcula o montante destinado aos advogados e fraciona
        $valorBase = (float) $lancamento->valor;
        $montanteAdvogados = $valorBase * ($percentualTotalDaCausa / 100);
        $valorIndividual = round($montanteAdvogados / count($advogadosIds), 2);

        DB::transaction(function () use ($lancamento, $advogadosIds, $valorIndividual, $percentualTotalDaCausa) {
            foreach ($advogadosIds as $advogadoId) {
                RateioHonorario::create([
                    'lancamento_financeiro_id' => $lancamento->id,
                    'user_id' => $advogadoId,
                    'valor' => $valorIndividual,
                    'percentual' => $percentualTotalDaCausa,
                    'tipo_rateio' => 'exito',
                ]);
            }
        });
    }

    /**
     * Calcula e registra o rateio por horas trabalhadas.
     *
     * @param  array<int, float>  $horasPorAdvogado  ex: ['user_id' => horas_trabalhadas]
     */
    public function calcularRateioHoras(
        LancamentoFinanceiro $lancamento,
        array $horasPorAdvogado
    ): void {
        if (empty($horasPorAdvogado)) {
            return;
        }

        $totalHoras = array_sum($horasPorAdvogado);
        if ($totalHoras <= 0) {
            return;
        }

        $valorBase = (float) $lancamento->valor;
        $valorPorHora = $valorBase / $totalHoras;

        DB::transaction(function () use ($lancamento, $horasPorAdvogado, $valorPorHora, $totalHoras) {
            foreach ($horasPorAdvogado as $advogadoId => $horas) {
                $valorIndividual = round($horas * $valorPorHora, 2);
                $percentual = round(($horas / $totalHoras) * 100, 2);

                RateioHonorario::create([
                    'lancamento_financeiro_id' => $lancamento->id,
                    'user_id' => $advogadoId,
                    'valor' => $valorIndividual,
                    'percentual' => $percentual,
                    'tipo_rateio' => 'horas',
                ]);
            }
        });
    }
}
