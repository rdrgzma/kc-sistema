<?php

namespace App\Enums;

enum ModalidadeAtividade: string
{
    case ONLINE = 'online';
    case PRESENCIAL = 'presencial';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'On-line',
            self::PRESENCIAL => 'Presencial',
        };
    }
}
