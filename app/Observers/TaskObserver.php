<?php

namespace App\Observers;

use App\Models\Bucket;
use App\Models\Task;
use App\Models\TimelineEvent;

class TaskObserver
{
    /**
     * Regista quando a tarefa é criada.
     */
    public function created(Task $task): void
    {
        $this->logEvent($task, 'task_created', "Tarefa '{$task->title}' criada.");
    }

    /**
     * Regista alterações de coluna (Bucket) ou outras mudanças importantes.
     */
    public function updated(Task $task): void
    {
        if ($task->isDirty('bucket_id')) {
            $oldBucket = Bucket::find($task->getOriginal('bucket_id'));

            $this->logEvent(
                $task,
                'status_changed',
                "Moveu a tarefa de '{$oldBucket->name}' para '{$task->bucket->name}'"
            );
        }

        // Pode adicionar outras verificações aqui, como alteração de responsável, etc.
        if ($task->isDirty('assigned_to')) {
            $novoResponsavel = $task->assignee ? $task->assignee->name : 'Ninguém';
            $this->logEvent($task, 'assignee_changed', "Responsável alterado para: {$novoResponsavel}");
        }
    }

    /**
     * Método auxiliar para centralizar a criação de eventos e o "Bubbling" para a entidade mãe.
     */
    private function logEvent(Task $task, string $type, string $description): void
    {
        $userId = auth()->id(); // Suportará null graças à sua migration 2026_03_27_183912

        // 1. Regista o evento na própria Tarefa
        TimelineEvent::create([
            'timelineable_type' => Task::class,
            'timelineable_id' => $task->id,
            'user_id' => $userId,
            'tipo' => 'A', // Administrativo
            'descricao' => $description,
            'data_evento' => now(),
        ]);

        // 2. "Bubbling": Propaga o evento para a entidade mãe (Processo, Pessoa), se existir
        if ($task->taskable) {
            TimelineEvent::create([
                'timelineable_type' => $task->taskable_type,
                'timelineable_id' => $task->taskable_id,
                'user_id' => $userId,
                'tipo' => 'A',
                'descricao' => "[Tarefa: {$task->title}] - ".$description,
                'data_evento' => now(),
            ]);
        }
    }
}
