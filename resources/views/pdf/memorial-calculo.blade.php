<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $calculo->titulo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { font-size: 18px; margin: 0; }
        h2 { font-size: 14px; margin: 5px 0; color: #555; }
        .params { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; }
        .params p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #aaa; }
        th { background-color: #f4f4f4; padding: 5px; text-align: center; font-size: 11px; }
        td { padding: 5px; text-align: right; font-size: 11px; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .totais { background-color: #e9ecef; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Memorial de Cálculo - {{ $calculo->titulo }}</h1>
        <h2>Atualizado até: {{ $calculo->data_atualizacao->format('d/m/Y') }}</h2>
    </div>

    <div class="params">
        <p><strong>Processo:</strong> {{ $calculo->processo ? $calculo->processo->numero_processo : 'Avulso (Não vinculado)' }}</p>
        <p><strong>Índice de Correção:</strong> {{ $calculo->indexador->nome }}</p>
        
        @php
            $tipoJuros = $calculo->parametros['juros']['tipo'] ?? 'percentual';
            $taxaMensal = ($tipoJuros === 'selic') ? '0,00% a.m. (Embutido no Índice SELIC)' : '1,00% a.m.';
        @endphp
        
        <p><strong>Tipo de Juros:</strong> {{ ucfirst($tipoJuros) }}</p>
        <p><strong>Taxa de Juros Aplicada:</strong> {{ $taxaMensal }}</p>
        <p><strong>Pro-Rata:</strong> {{ ($calculo->parametros['juros']['pro_rata'] ?? true) ? 'Sim' : 'Não' }}</p>
        <p><strong>Calculado por:</strong> {{ auth()->user()?->name ?? 'Usuário' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Vencimento</th>
                <th>Vlr Original</th>
                <th class="text-center">Fator</th>
                <th>Vlr Corrigido</th>
                <th class="text-center">Dias (Juros)</th>
                <th>Juros</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calculo->parametros['memorial'] ?? [] as $linha)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($linha['data'])->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($linha['valor_original'], 2, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($linha['fator'], 6, ',', '.') }}</td>
                    <td>R$ {{ number_format($linha['valor_corrigido'], 2, ',', '.') }}</td>
                    <td class="text-center">{{ $linha['dias'] }}</td>
                    <td>R$ {{ number_format($linha['juros'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($linha['valor_final'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhuma parcela processada.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totais font-bold">
                <td class="text-left">TOTAIS</td>
                <td>R$ {{ number_format($calculo->valor_original, 2, ',', '.') }}</td>
                <td>-</td>
                <td>R$ {{ number_format($calculo->valor_corrigido, 2, ',', '.') }}</td>
                <td>-</td>
                <td>R$ {{ number_format($calculo->juros_total, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($calculo->valor_final, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
