<?php

declare(strict_types=1);

namespace App\Domain\Calculos;

use App\Domain\Calculos\Contracts\CalculadorJurosInterface;
use DateTimeImmutable;

class CalculadorJuros implements CalculadorJurosInterface
{
    public function calcularDiasProRata(DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): array
    {
        if ($dataInicio > $dataFim) {
            return ['meses_cheios' => 0, 'fator_fracionado' => 0.0, 'dias_fracionados' => 0, 'dias_totais' => 0];
        }

        $diff = $dataInicio->diff($dataFim);

        $mesesCheios = ($diff->y * 12) + $diff->m;
        $diasRestantes = $diff->d;

        $fatorFracionado = 0.0;
        if ($diasRestantes > 0) {
            // Usa o total de dias do mês de dataFim para lidar corretamente com a fração de dias
            $totalDiasMesFim = (int) $dataFim->format('t');
            $fatorFracionado = $diasRestantes / $totalDiasMesFim;
        }

        return [
            'meses_cheios' => $mesesCheios,
            'fator_fracionado' => $fatorFracionado,
            'dias_fracionados' => $diasRestantes,
            'dias_totais' => $diff->days,
        ];
    }

    public function calcularSimples(float $valorBase, float $taxaMensal, DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): float
    {
        $tempo = $this->calcularDiasProRata($dataInicio, $dataFim);
        $totalMeses = $tempo['meses_cheios'] + $tempo['fator_fracionado'];

        $juros = $valorBase * ($taxaMensal / 100) * $totalMeses;

        return round($juros, 2);
    }

    public function calcularCompostos(float $valorBase, float $taxaMensal, DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): float
    {
        $tempo = $this->calcularDiasProRata($dataInicio, $dataFim);
        $totalMeses = $tempo['meses_cheios'] + $tempo['fator_fracionado'];

        $taxa = $taxaMensal / 100;
        $montante = $valorBase * pow((1 + $taxa), $totalMeses);
        $juros = $montante - $valorBase;

        return round($juros, 2);
    }
}
