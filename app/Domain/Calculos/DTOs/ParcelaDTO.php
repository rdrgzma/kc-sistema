<?php

declare(strict_types=1);

namespace App\Domain\Calculos\DTOs;

use DateTimeImmutable;

readonly class ParcelaDTO
{
    public function __construct(
        public DateTimeImmutable $data,
        public float $valor,
        public string $tipo
    ) {}
}
