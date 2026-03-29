<?php

namespace App\Enums;

enum DurationUnit: string
{
    case HORAS = 'horas';
    case DIAS = 'dias';

    public function getLabel(): string
    {
        return match ($this) {
            self::HORAS => 'Hora(s)',
            self::DIAS => 'Dia(s)',
        };
    }
}
