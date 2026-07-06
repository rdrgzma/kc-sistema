<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Analytics e Auditoria</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 40px; color: #333; font-size: 10px; }
        .header { text-align: left; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .subtitle { color: #666; margin-top: 5px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; background: #f8f8f8; padding: 10px; border-bottom: 1px solid #ddd; text-transform: uppercase; font-size: 8px; color: #555; }
        td { padding: 10px; border-bottom: 1px solid #eee; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; color: #999; font-size: 8px; padding-top: 10px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }}</div>
        <div class="subtitle">Relatório de Analytics e Auditoria</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%">Data/Hora</th>
                <th style="width: 25%">Operador</th>
                <th style="width: 30%">Ação / Descrição</th>
                <th style="width: 30%">Registro Alvo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $act)
            <tr>
                <td>{{ \Carbon\Carbon::parse($act->created_at)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $act->causer?->name ?? 'Sistema' }}</td>
                <td>{{ $act->description }}</td>
                <td>
                    @if($act->subject)
                        @if($act->subject_type === \App\Models\Pessoa::class)
                            Pessoa: {{ $act->subject->nome_razao }}
                        @elseif($act->subject_type === \App\Models\Processo::class)
                            Processo: {{ $act->subject->numero_processo }}
                        @else
                            {{ class_basename($act->subject_type) }}: {{ $act->subject->nome ?? $act->subject->title ?? "#{$act->subject_id}" }}
                        @endif
                    @else
                        {{ $act->subject_type ? class_basename($act->subject_type).' #'.$act->subject_id : 'Não disponível' }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }} - {{ config('app.url') }}
    </div>
</body>
</html>
