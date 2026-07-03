<?php

use App\Domain\Calculos\CalculadorJuros;

it('calcula corretamente a diferença de meses e fração em ano não bissexto', function () {
    $calculador = new CalculadorJuros;
    $inicio = new DateTimeImmutable('2023-01-01');
    $fim = new DateTimeImmutable('2023-02-15');

    $resultado = $calculador->calcularDiasProRata($inicio, $fim);

    expect($resultado['meses_cheios'])->toBe(1);
    expect($resultado['dias_fracionados'])->toBe(14); // 15 - 1 = 14 dias
    expect(round($resultado['fator_fracionado'], 4))->toBe(0.5000); // 14 / 28 = 0.5
});

it('calcula corretamente a diferença em ano bissexto (Fevereiro 29 dias)', function () {
    $calculador = new CalculadorJuros;
    $inicio = new DateTimeImmutable('2024-01-01');
    $fim = new DateTimeImmutable('2024-02-15');

    $resultado = $calculador->calcularDiasProRata($inicio, $fim);

    expect($resultado['meses_cheios'])->toBe(1);
    expect($resultado['dias_fracionados'])->toBe(14);
    expect(round($resultado['fator_fracionado'], 4))->toBe(0.4828); // 14 / 29 = 0.48275
});

it('retorna zero se a data inicial for maior que a final', function () {
    $calculador = new CalculadorJuros;
    $inicio = new DateTimeImmutable('2024-05-01');
    $fim = new DateTimeImmutable('2024-04-01');

    $resultado = $calculador->calcularDiasProRata($inicio, $fim);

    expect($resultado['meses_cheios'])->toBe(0);
    expect($resultado['fator_fracionado'])->toBe(0.0);
    expect($resultado['dias_fracionados'])->toBe(0);
});

it('calcula juros simples (pró-rata)', function () {
    $calculador = new CalculadorJuros;
    $inicio = new DateTimeImmutable('2023-01-01');
    $fim = new DateTimeImmutable('2023-01-16'); // Diff: 15 dias (16-1)

    // 15 / 31 = 0.48387
    $juros = $calculador->calcularSimples(1000.0, 1.0, $inicio, $fim);

    expect($juros)->toBe(4.84); // 1000 * 0.01 * 0.48387 = 4.8387 -> 4.84
});

it('calcula juros compostos (pró-rata)', function () {
    $calculador = new CalculadorJuros;
    $inicio = new DateTimeImmutable('2023-01-01');
    $fim = new DateTimeImmutable('2023-03-16'); // Diff: 2 meses e 15 dias

    // P * (1.01)^(2 + 15/31)
    $juros = $calculador->calcularCompostos(1000.0, 1.0, $inicio, $fim);

    expect($juros)->toBe(25.02);
});
