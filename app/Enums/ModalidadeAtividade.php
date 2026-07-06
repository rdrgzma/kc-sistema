<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ModalidadeAtividade: string implements HasLabel
{
    case ONLINE = 'online';
    case PRESENCIAL = 'presencial';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ONLINE => 'On-line',
            self::PRESENCIAL => 'Presencial',
        };
    }
}
