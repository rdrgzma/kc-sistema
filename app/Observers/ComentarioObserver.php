<?php

namespace App\Observers;

use App\Models\Comentario;
use App\Models\Processo;
use App\Models\Task;

class ComentarioObserver
{
    public function created(Comentario $comentario): void
    {
        if ($comentario->commentable_type === Task::class) {
            $task = $comentario->commentable;
            if ($task && $task->processo_id) {
                $processo = Processo::find($task->processo_id);
                if ($processo) {
                    $processo->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Novo comentário na tarefa '{$task->title}'.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }
    }

    public function updated(Comentario $comentario): void
    {
        if ($comentario->commentable_type === Task::class) {
            $task = $comentario->commentable;
            if ($task && $task->processo_id) {
                $processo = Processo::find($task->processo_id);
                if ($processo) {
                    $processo->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Comentário editado na tarefa '{$task->title}'.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }
    }

    public function deleted(Comentario $comentario): void
    {
        if ($comentario->commentable_type === Task::class) {
            $task = $comentario->commentable;
            if ($task && $task->processo_id) {
                $processo = Processo::find($task->processo_id);
                if ($processo) {
                    $processo->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Comentário removido da tarefa '{$task->title}'.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }
    }
}
