<?php

declare(strict_types=1);

namespace App\Domain\Calculos\Contracts;

use DateTimeImmutable;

interface CalculadorJurosInterface
{
    public function calcularSimples(float $valorBase, float $taxaMensal, DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): float;

    public function calcularCompostos(float $valorBase, float $taxaMensal, DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): float;

    public function calcularDiasProRata(DateTimeImmutable $dataInicio, DateTimeImmutable $dataFim): array;
}
