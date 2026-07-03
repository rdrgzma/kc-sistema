<?php

namespace App\DTOs;

use DateTimeInterface;

class PublicacaoDTO
{
    public function __construct(
        public readonly int $processoId,
        public readonly string $textoPublicacao,
        public readonly DateTimeInterface $dataPublicacao,
    ) {}
}
