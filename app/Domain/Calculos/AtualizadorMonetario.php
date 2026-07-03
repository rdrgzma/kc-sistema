<?php

declare(strict_types=1);

namespace App\Domain\Calculos;

use App\Domain\Calculos\Contracts\AtualizadorMonetarioInterface;
use DateTimeImmutable;
use RuntimeException;

class AtualizadorMonetario implements AtualizadorMonetarioInterface
{
    /**
     * @var array<string, float> array no formato 'Y-m-d' => fator
     */
    private array $fatores = [];

    public function setFatores(array $fatores): void
    {
        $this->fatores = $fatores;
    }

    public function getFator(DateTimeImmutable $data): float
    {
        $key = $data->format('Y-m-01'); // Sempre pega o fator do mês base

        if (! isset($this->fatores[$key])) {
            $availableKeys = array_keys($this->fatores);
            if (empty($availableKeys)) {
                return 1.0;
            }
            rsort($availableKeys);

            return (float) $this->fatores[$availableKeys[0]];
        }

        return (float) $this->fatores[$key];
    }

    public function atualizar(float $valor, DateTimeImmutable $dataBase, DateTimeImmutable $dataAtualizacao): float
    {
        $fatorBase = $this->getFator($dataBase);
        $fatorAtualizacao = $this->getFator($dataAtualizacao);

        if ($fatorBase === 0.0) {
            throw new RuntimeException("Fator base na data {$dataBase->format('Y-m')} é zero, impossível calcular.");
        }

        return $valor / $fatorBase * $fatorAtualizacao;
    }
}
