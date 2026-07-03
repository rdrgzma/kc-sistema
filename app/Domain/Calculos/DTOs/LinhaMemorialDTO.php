<?php

declare(strict_types=1);

namespace App\Domain\Calculos\DTOs;

use DateTimeImmutable;

readonly class LinhaMemorialDTO
{
    public function __construct(
        public DateTimeImmutable $data,
        public float $valorOriginal,
        public float $fator,
        public float $valorCorrigido,
        public int $dias,
        public float $juros,
        public float $valorFinal
    ) {}
}
