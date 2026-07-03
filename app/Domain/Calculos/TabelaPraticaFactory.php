<?php

declare(strict_types=1);

namespace App\Domain\Calculos;

class TabelaPraticaFactory
{
    /**
     * Mescla múltiplos arrays de fatores (data => fator),
     * priorizando as fontes informadas por último na lista.
     *
     * @param  array<string, float>  ...$fontesFatores
     * @return array<string, float>
     */
    public function unificarHistorico(array ...$fontesFatores): array
    {
        $unificado = [];

        foreach ($fontesFatores as $fonte) {
            foreach ($fonte as $data => $fator) {
                $unificado[$data] = $fator;
            }
        }

        ksort($unificado);

        return $unificado;
    }
}
