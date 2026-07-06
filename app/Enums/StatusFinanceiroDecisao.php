<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusFinanceiroDecisao: string implements HasLabel
{
    case SUB_JUDICE = 'sub_judice';
    case TRANSITO_EM_JULGADO = 'transito_em_julgado';
    case SEM_PERDA_ECONOMICA = 'sem_perda_economica';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUB_JUDICE => 'Sub judice',
            self::TRANSITO_EM_JULGADO => 'Trânsito em Julgado',
            self::SEM_PERDA_ECONOMICA => 'Sem perda econômica',
        };
    }
}
