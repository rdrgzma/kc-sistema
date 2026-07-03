<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeslocamentosExport implements FromCollection, WithHeadings, WithMapping
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
            'Membro da Equipe',
            'Nº do Processo',
            'Tipo de Atividade',
            'Local de Destino',
            'Data',
            'Início',
            'Fim',
            'Duração (Minutos)',
        ];
    }

    public function map($record): array
    {
        return [
            $record->user?->name ?? 'Não informado',
            $record->processo?->numero_processo ?? 'Não associado',
            $record->tipo_atividade?->getLabel() ?? '',
            $record->local ?? '',
            $record->data_atividade ? $record->data_atividade->format('d/m/Y') : '',
            $record->hora_inicio ? Carbon::parse($record->hora_inicio)->format('H:i') : '',
            $record->hora_fim ? Carbon::parse($record->hora_fim)->format('H:i') : '',
            "{$record->tempo_deslocamento} min",
        ];
    }
}
