<?php

namespace App\Filament\Exports;

use App\Models\ApontamentoTempo;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ApontamentoExporter extends Exporter
{
    protected static ?string $model = ApontamentoTempo::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('data_atividade')
                ->label('Data')
                ->state(fn (ApontamentoTempo $record) => $record->data_atividade?->format('d/m/Y')),

            ExportColumn::make('user.name')
                ->label('Colaborador'),

            ExportColumn::make('user.equipes.nome')
                ->label('Equipe')
                ->state(fn (ApontamentoTempo $record) => $record->user?->equipes?->pluck('nome')?->implode(', ') ?: '-'),

            ExportColumn::make('tipo_atividade')
                ->label('Atividade')
                ->state(fn (ApontamentoTempo $record) => $record->tipo_atividade?->getLabel()),

            ExportColumn::make('processo.numero_processo')
                ->label('Processo')
                ->state(fn (ApontamentoTempo $record) => $record->processo?->numero_processo ?: 'Não associado'),

            ExportColumn::make('local')
                ->label('Local')
                ->state(fn (ApontamentoTempo $record) => $record->local ?: '-'),

            ExportColumn::make('modalidade')
                ->label('Modalidade')
                ->state(fn (ApontamentoTempo $record) => $record->modalidade?->getLabel()),

            ExportColumn::make('hora_inicio')
                ->label('Início')
                ->state(fn (ApontamentoTempo $record) => $record->hora_inicio),

            ExportColumn::make('hora_fim')
                ->label('Fim')
                ->state(fn (ApontamentoTempo $record) => $record->hora_fim),

            ExportColumn::make('tempo_deslocamento')
                ->label('Tempo Total (Minutos)')
                ->state(fn (ApontamentoTempo $record) => $record->tempo_deslocamento),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Sua exportação de apontamentos de tempo foi concluída. '.number_format($export->successful_rows).' '.str('registro')->plural($export->successful_rows).' exportados.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('registro')->plural($failedRowsCount).' falharam.';
        }

        return $body;
    }
}
