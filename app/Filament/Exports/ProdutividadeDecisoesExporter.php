<?php

namespace App\Filament\Exports;

use App\Models\Sentenca;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProdutividadeDecisoesExporter extends Exporter
{
    protected static ?string $model = Sentenca::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('processos')
                ->label('Processo')
                ->state(fn (Sentenca $record) => $record->processos->pluck('numero_processo')->implode(', ') ?: '-'),

            ExportColumn::make('clientes')
                ->label('Cliente')
                ->state(fn (Sentenca $record) => $record->processos->map(fn ($p) => $p->pessoa?->nome_razao)->filter()->unique()->implode(', ') ?: '-'),

            ExportColumn::make('tipo_decisao')
                ->label('Tipo de Decisão')
                ->state(fn (Sentenca $record) => $record->tipo_decisao ?: '-'),

            ExportColumn::make('classificacao')
                ->label('Classificação')
                ->state(fn (Sentenca $record) => $record->classificacao?->getLabel()),

            ExportColumn::make('valor_economia')
                ->label('Valor Economia (R$)')
                ->state(fn (Sentenca $record) => $values = $record->valor_economia),

            ExportColumn::make('valor_perda')
                ->label('Valor Perda (R$)')
                ->state(fn (Sentenca $record) => $values = $record->valor_perda),

            ExportColumn::make('status_financeiro')
                ->label('Status Financeiro')
                ->state(fn (Sentenca $record) => $record->status_financeiro?->getLabel()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Sua exportação de decisões de produtividade foi concluída. '.number_format($export->successful_rows).' '.str('registro')->plural($export->successful_rows).' exportados.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('registro')->plural($failedRowsCount).' falharam.';
        }

        return $body;
    }
}
