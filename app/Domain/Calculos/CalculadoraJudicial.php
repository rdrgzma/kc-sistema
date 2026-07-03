<?php

declare(strict_types=1);

namespace App\Domain\Calculos;

use App\Domain\Calculos\Contracts\AtualizadorMonetarioInterface;
use App\Domain\Calculos\Contracts\CalculadorJurosInterface;
use App\Domain\Calculos\DTOs\LinhaMemorialDTO;
use App\Domain\Calculos\DTOs\ParcelaDTO;
use DateTimeImmutable;

class CalculadoraJudicial
{
    public function __construct(
        private readonly AtualizadorMonetarioInterface $atualizador,
        private readonly CalculadorJurosInterface $calculadorJuros
    ) {}

    /**
     * @param  ParcelaDTO[]  $parcelas
     * @return LinhaMemorialDTO[]
     */
    public function calcularMemorial(
        array $parcelas,
        DateTimeImmutable $dataAtualizacao,
        float $taxaJurosMensal = 1.0,
        bool $jurosCompostos = false
    ): array {
        $memorial = [];
        $fatorAtualizacao = $this->atualizador->getFator($dataAtualizacao);

        foreach ($parcelas as $parcela) {
            $fatorParcela = $this->atualizador->getFator($parcela->data);
            $fatorDivisao = 1.0;
            if ($fatorParcela > 0) {
                $fatorDivisao = $fatorAtualizacao / $fatorParcela;
            }

            $valorCorrigido = round($this->atualizador->atualizar($parcela->valor, $parcela->data, $dataAtualizacao), 2);

            $tempo = $this->calculadorJuros->calcularDiasProRata($parcela->data, $dataAtualizacao);

            if ($jurosCompostos) {
                $juros = $this->calculadorJuros->calcularCompostos($valorCorrigido, $taxaJurosMensal, $parcela->data, $dataAtualizacao);
            } else {
                $juros = $this->calculadorJuros->calcularSimples($valorCorrigido, $taxaJurosMensal, $parcela->data, $dataAtualizacao);
            }

            $valorFinal = $valorCorrigido + $juros;

            $memorial[] = new LinhaMemorialDTO(
                data: $parcela->data,
                valorOriginal: $parcela->valor,
                fator: $fatorDivisao,
                valorCorrigido: $valorCorrigido,
                dias: $tempo['dias_totais'],
                juros: $juros,
                valorFinal: $valorFinal
            );
        }

        return $memorial;
    }
}
