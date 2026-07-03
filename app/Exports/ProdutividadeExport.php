<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdutividadeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Usuário',
            'Tarefas Atribuídas',
            'Tarefas Concluídas',
            'Média de Inícios/Tarefa',
            'Taxa de Retrabalho',
            'Repetições',
            'Tempo Total Apontado',
        ];
    }

    public function map($record): array
    {
        $conclusoes = $record->soma_conclusoes ?? 0;
        $concluidas = $record->tarefas_concluidas ?? 0;
        $taxa = $concluidas > 0 ? ($conclusoes / $concluidas) : 0;
        $repeticoes = $conclusoes - $concluidas;

        $totalMinutos = $record->apontamentosTempo->sum('tempo_deslocamento');
        $horas = floor($totalMinutos / 60);
        $minutos = $totalMinutos % 60;
        $tempo = "{$horas}h {$minutos}m";

        return [
            $record->name,
            $record->total_tarefas_atribuidas ?? 0,
            $record->tarefas_concluidas ?? 0,
            number_format((float) ($record->media_inicios_por_tarefa ?? 0), 2, ',', '.'),
            number_format($taxa, 2, ',', '.'),
            $repeticoes > 0 ? $repeticoes : 0,
            $tempo,
        ];
    }
}
