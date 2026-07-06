<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DurationUnit: string implements HasLabel
{
    case HORAS = 'horas';
    case DIAS = 'dias';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HORAS => 'Hora(s)',
            self::DIAS => 'Dia(s)',
        };
    }
}
