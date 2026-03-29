<?php

namespace App\Services;

use App\Enums\DurationUnit;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class TaskDurationService
{
    /**
     * Calcula o due_date com base em uma duração (horas ou dias).
     */
    public function calculateDueDate(int $value, DurationUnit $unit, ?CarbonInterface $startDate = null): CarbonInterface
    {
        // Se não passar data inicial, assume o momento atual
        $date = $startDate ? $startDate->copy() : now();

        return match ($unit) {
            DurationUnit::HORAS => $date->addHours($value),
            DurationUnit::DIAS => $date->addDays($value),
        };
    }

    /**
     * Calcula a duração (valor e unidade) com base em um due_date.
     */
    public function calculateDuration(CarbonInterface|string $dueDate, ?CarbonInterface $startDate = null): array
    {
        if (is_string($dueDate)) {
            $dueDate = Carbon::parse($dueDate);
        }

        $date = $startDate ? $startDate->copy() : now();

        // Se a data de entrega for menor que a data base, zeramos a duração
        if ($dueDate->lessThanOrEqualTo($date)) {
            return [
                'value' => 0,
                'unit' => DurationUnit::HORAS,
            ];
        }

        $diffInHours = $date->diffInHours($dueDate);

        // Se a diferença for de 24h ou mais, arredondamos para DIAS
        if ($diffInHours >= 24) {
            return [
                'value' => $date->diffInDays($dueDate),
                'unit' => DurationUnit::DIAS,
            ];
        }

        // Caso contrário, mantemos em HORAS
        return [
            'value' => $diffInHours,
            'unit' => DurationUnit::HORAS,
        ];
    }
}
