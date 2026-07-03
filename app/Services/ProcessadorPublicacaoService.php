<?php

namespace App\Services;

use App\DTOs\PublicacaoDTO;
use App\Models\Bucket;
use App\Models\Planner;
use App\Models\Processo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessadorPublicacaoService
{
    public function processar(PublicacaoDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $processo = Processo::findOrFail($dto->processoId);

            // 1. Registra o andamento na Timeline do Processo
            $processo->timelineEvents()->create([
                'tipo' => 'J', // Judicial (publicação oficial)
                'descricao' => 'Publicação Oficial: '.$dto->textoPublicacao,
                'data_evento' => $dto->dataPublicacao,
                'user_id' => null, // Ou o ID de um robô/sistema
            ]);

            // 2. Injeta no Kanban (Criação de Tarefa)
            // Tenta pegar o bucket 'Backlog' ou o primeiro disponível
            $planner = Planner::firstOrCreate(['name' => 'Quadro Geral']);

            $bucket = Bucket::firstOrCreate(
                ['name' => 'Backlog'],
                ['sort' => 0, 'color' => 'gray', 'planner_id' => $planner->id]
            );

            $dueDate = Carbon::instance($dto->dataPublicacao)->addWeekdays(2);

            $processo->tasks()->create([
                'bucket_id' => $bucket->id,
                'title' => 'Analisar Nova Publicação',
                'description' => $dto->textoPublicacao,
                'due_date' => $dueDate,
                'urgency' => 'normal', // \App\Enums\TaskUrgency::NORMAL
                'sort' => 0,
            ]);
        });
    }
}
