<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaskUrgency: string implements HasColor, HasLabel
{
    case BAIXA = 'baixa';
    case NORMAL = 'normal';
    case ALTA = 'alta';
    case URGENTE = 'urgente';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BAIXA => 'Baixa',
            self::NORMAL => 'Normal',
            self::ALTA => 'Alta',
            self::URGENTE => 'Urgente!',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BAIXA => 'gray',
            self::NORMAL => 'info',
            self::ALTA => 'warning',
            self::URGENTE => 'danger',
        };
    }
}
