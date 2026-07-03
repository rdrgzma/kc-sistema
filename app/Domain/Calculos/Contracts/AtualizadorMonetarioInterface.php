<?php

declare(strict_types=1);

namespace App\Domain\Calculos\Contracts;

use DateTimeImmutable;

interface AtualizadorMonetarioInterface
{
    public function setFatores(array $fatores): void;

    public function getFator(DateTimeImmutable $data): float;

    public function atualizar(float $valor, DateTimeImmutable $dataBase, DateTimeImmutable $dataAtualizacao): float;
}
