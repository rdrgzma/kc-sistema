<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório Financeiro</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 40px; color: #333; font-size: 11px; }
        .header { text-align: left; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .subtitle { color: #666; margin-top: 5px; font-weight: bold; }
        
        .summary-box { margin-bottom: 30px; border: 1px solid #eee; padding: 15px; border-radius: 8px; }
        .summary-item { display: inline-block; width: 32%; }
        .label { color: #888; font-size: 9px; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .value { font-size: 14px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; background: #f8f8f8; padding: 10px; border-bottom: 1px solid #ddd; text-transform: uppercase; font-size: 9px; color: #555; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .type-receita { color: #16a34a; font-weight: bold; }
        .type-despesa { color: #dc2626; font-weight: bold; }
        .status-badge { font-size: 9px; background: #eee; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; color: #666; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; color: #999; font-size: 9px; padding-top: 10px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }}</div>
        <div class="subtitle">Relatório de Gestão Financeira</div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="label">Total de Receitas</div>
            <div class="value" style="color: #16a34a;">R$ {{ number_format($resumo['total_receita'], 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total de Despesas</div>
            <div class="value" style="color: #dc2626;">R$ {{ number_format($resumo['total_despesa'], 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Resultado</div>
            <div class="value">R$ {{ number_format($resumo['total_receita'] - $resumo['total_despesa'], 2, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vencimento</th>
                <th>Origem</th>
                <th>Tipo</th>
                <th>Categoria</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lancamentos as $l)
            <tr>
                <td>{{ \Carbon\Carbon::parse($l->data_vencimento)->format('d/m/Y') }}</td>
                <td>
                    @if($l->lancamentable)
                        @if($l->lancamentable_type === \App\Models\Pessoa::class)
                            Cliente: {{ $l->lancamentable->nome_razao }}
                        @else
                            Proc: {{ $l->lancamentable->numero_processo }}
                        @endif
                    @else
                        Manual
                    @endif
                </td>
                <td class="type-{{ $l->tipo }}">{{ ucfirst($l->tipo) }}</td>
                <td>{{ $l->categoria->nome ?? '-' }}</td>
                <td>{{ $l->descricao }}</td>
                <td>R$ {{ number_format($l->valor, 2, ',', '.') }}</td>
                <td><span class="status-badge">{{ $l->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }} - {{ config('app.url') }}
    </div>
</body>
</html>
