<?php

namespace App\Enums;

enum ClassificacaoDecisao: string
{
    case FAVORAVEL = 'favoravel';
    case DESFAVORAVEL = 'desfavoravel';
    case PARCIAL = 'parcial';

    public function getLabel(): string
    {
        return match ($this) {
            self::FAVORAVEL => 'Favorável',
            self::DESFAVORAVEL => 'Desfavorável',
            self::PARCIAL => 'Parcial',
        };
    }
}
