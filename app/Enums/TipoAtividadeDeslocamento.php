<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoAtividadeDeslocamento: string implements HasLabel
{
    case AUDIENCIA = 'audiencia';
    case DILIGENCIA = 'diligencia';
    case PECA_RELATORIO = 'peca_relatorio';
    case REUNIAO = 'reuniao';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AUDIENCIA => 'Audiência',
            self::DILIGENCIA => 'Diligência',
            self::PECA_RELATORIO => 'Peça/Relatório',
            self::REUNIAO => 'Reunião',
        };
    }
}
