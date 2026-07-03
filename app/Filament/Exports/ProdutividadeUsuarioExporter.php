<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProdutividadeUsuarioExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Usuário'),
            ExportColumn::make('total_tarefas_atribuidas')
                ->label('Tarefas Atribuídas'),
            ExportColumn::make('tarefas_concluidas')
                ->label('Tarefas Concluídas'),
            ExportColumn::make('media_inicios_por_tarefa')
                ->label('Média de Inícios/Tarefa')
                ->state(function (User $record) {
                    return number_format((float) ($record->media_inicios_por_tarefa ?? 0), 2, ',', '.');
                }),
            ExportColumn::make('taxa_retrabalho')
                ->label('Taxa de Retrabalho')
                ->state(function (User $record) {
                    $conclusoes = $record->soma_conclusoes ?? 0;
                    $concluidas = $record->tarefas_concluidas ?? 0;

                    if ($concluidas == 0) {
                        return '0';
                    }

                    $taxa = $conclusoes / $concluidas;
                    $retrabalhosExtras = $conclusoes - $concluidas;

                    $formatado = number_format($taxa, 2, ',', '.');

                    return "{$formatado} (".($retrabalhosExtras > 0 ? "+{$retrabalhosExtras} repetições" : '0 repetições').')';
                }),
            ExportColumn::make('tempo_total')
                ->label('Tempo Total Apontado')
                ->state(function (User $record) {
                    $totalMinutos = $record->apontamentosTempo->sum('tempo_deslocamento');

                    if ($totalMinutos === 0) {
                        return '0h 0m';
                    }

                    $horas = floor($totalMinutos / 60);
                    $minutos = $totalMinutos % 60;

                    return "{$horas}h {$minutos}m";
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de produtividade foi concluída e o arquivo está pronto para download.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' linhas falharam ao exportar.';
        }

        return $body;
    }
}
