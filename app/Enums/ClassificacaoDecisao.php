<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClassificacaoDecisao: string implements HasLabel
{
    case FAVORAVEL = 'favoravel';
    case DESFAVORAVEL = 'desfavoravel';
    case PARCIAL = 'parcial';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FAVORAVEL => 'Favorável',
            self::DESFAVORAVEL => 'Desfavorável',
            self::PARCIAL => 'Parcial',
        };
    }
}
