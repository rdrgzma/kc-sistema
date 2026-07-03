<?php

use App\Domain\Calculos\AtualizadorMonetario;
use App\Domain\Calculos\CalculadoraJudicial;
use App\Domain\Calculos\CalculadorJuros;
use App\Domain\Calculos\DTOs\ParcelaDTO;

it('calcula o memorial corretamente usando mock ou instâncias reais com juros simples', function () {
    $atualizador = new AtualizadorMonetario;

    $atualizador->setFatores([
        '2023-01-01' => 100.0,
        '2023-02-01' => 101.0,
        '2023-03-01' => 102.0,
    ]);

    $calculadorJuros = new CalculadorJuros;
    $calculadora = new CalculadoraJudicial($atualizador, $calculadorJuros);

    $parcelas = [
        new ParcelaDTO(new DateTimeImmutable('2023-01-01'), 1000.0, 'principal'),
    ];

    $dataAtualizacao = new DateTimeImmutable('2023-03-15');

    $memorial = $calculadora->calcularMemorial(
        parcelas: $parcelas,
        dataAtualizacao: $dataAtualizacao,
        taxaJurosMensal: 1.0,
        jurosCompostos: false
    );

    expect($memorial)->toHaveCount(1);

    $linha = $memorial[0];

    expect($linha->valorCorrigido)->toBe(1020.0);
    expect($linha->fator)->toBe(1.02);
    expect($linha->juros)->toBe(25.01);
    expect($linha->valorFinal)->toBe(1020.0 + 25.01);
});
