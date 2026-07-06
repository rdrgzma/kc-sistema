<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoPecaProduzida: string implements HasLabel
{
    case PETICOES_EXPEDIENTE = 'peticoes_expediente';
    case CONTESTACAO = 'contestacao';
    case APELACAO = 'apelacao';
    case CRM_CRO_COREN = 'crm_cro_coren';
    case OUTROS = 'outros';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PETICOES_EXPEDIENTE => 'Petições de Expediente',
            self::CONTESTACAO => 'Contestação',
            self::APELACAO => 'Apelação',
            self::CRM_CRO_COREN => 'CRM/CRO/COREN',
            self::OUTROS => 'Outros',
        };
    }
}
