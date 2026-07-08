<?php

namespace App\Enums;

enum AcaoGR: string
{
    case Elaboracao = 'Elaboração';
    case Alteracao = 'Alteração';
    case Analise = 'Análise';
    case Reuniao = 'Reunião';

    public function getLabel(): string
    {
        return $this->value;
    }
}
