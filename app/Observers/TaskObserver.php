<?php

namespace App\Observers;

use App\Models\Bucket;
use App\Models\Pessoa;
use App\Models\Processo;
use App\Models\Task;

class TaskObserver
{
    public function created(Task $task): void
    {
        if ($task->processo_id) {
            $processo = Processo::find($task->processo_id);
            if ($processo) {
                $processo->timelineEvents()->create([
                    'tipo' => 'A',
                    'descricao' => "Nova tarefa registrada: {$task->title}.",
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            }

            // Propagate documents and pieces on creation
            $this->propagateToProcess($task);
        }
    }

    public function updating(Task $task): void
    {
        if ($task->isDirty('bucket_id') && $task->bucket_id) {
            $bucket = Bucket::find($task->bucket_id);
            if ($bucket) {
                $statusName = strtolower(trim($bucket->name));
                // Incrementa inícios se for uma coluna de "fazendo"
                if (in_array($statusName, ['in_progress', 'in progress', 'doing', 'em andamento', 'fazendo'])) {
                    $task->inicios_count++;
                }
                // Incrementa conclusões se for uma coluna de "concluído"
                elseif (in_array($statusName, ['completed', 'done', 'concluido', 'concluído', 'finalizado'])) {
                    $task->conclusoes_count++;
                }
            }
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('processo_id')) {
            $oldProcessoId = $task->getOriginal('processo_id');
            $newProcessoId = $task->processo_id;

            if ($oldProcessoId) {
                $oldProcesso = Processo::find($oldProcessoId);
                if ($oldProcesso) {
                    $oldProcesso->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Tarefa desassociada do processo: {$task->title}.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            if ($newProcessoId) {
                $newProcesso = Processo::find($newProcessoId);
                if ($newProcesso) {
                    $newProcesso->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Tarefa associada ao processo: {$task->title}.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }

        if ($task->wasChanged('bucket_id') && $task->bucket_id) {
            $bucket = Bucket::find($task->bucket_id);
            if ($bucket) {
                $statusName = strtolower(trim($bucket->name));
                $isCompleted = in_array($statusName, ['completed', 'done', 'concluido', 'concluído', 'finalizado']);
                $actionLabel = $isCompleted ? 'concluída' : 'reaberta / movida';

                if ($task->processo_id) {
                    $processo = Processo::find($task->processo_id);
                    if ($processo) {
                        $processo->timelineEvents()->create([
                            'tipo' => 'A',
                            'descricao' => "Tarefa {$actionLabel}: {$task->title} (Coluna: {$bucket->name}).",
                            'data_evento' => now(),
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                $task->timelineEvents()->create([
                    'tipo' => 'A',
                    'descricao' => "Tarefa movida para a coluna {$bucket->name}.",
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        if ($task->wasChanged(['processo_id', 'pessoa_id'])) {
            if ($task->processo_id || $task->pessoa_id) {
                $this->propagateToProcess($task);
            } else {
                $this->revertPropagation($task);
            }
        } else {
            // Title or other details changed, log on timeline if associated
            if ($task->wasChanged('title') && $task->processo_id) {
                $processo = Processo::find($task->processo_id);
                if ($processo) {
                    $processo->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Tarefa editada: {$task->title}.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }
    }

    public function deleted(Task $task): void
    {
        if ($task->processo_id) {
            $processo = Processo::find($task->processo_id);
            if ($processo) {
                $processo->timelineEvents()->create([
                    'tipo' => 'A',
                    'descricao' => "Tarefa excluída: {$task->title}.",
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }

    protected function propagateToProcess(Task $task): void
    {
        if ($task->processo_id) {
            // Update documents
            $task->documentos()->update([
                'documentable_type' => Processo::class,
                'documentable_id' => $task->processo_id,
            ]);

            // Update piece processual
            if ($task->pecaProcessual) {
                $task->pecaProcessual->update([
                    'processo_id' => $task->processo_id,
                ]);
            }
        } elseif ($task->pessoa_id) {
            // Update documents to belong to the client (Pessoa)
            $task->documentos()->update([
                'documentable_type' => Pessoa::class,
                'documentable_id' => $task->pessoa_id,
            ]);

            // Revert piece processual
            if ($task->pecaProcessual) {
                $task->pecaProcessual->update([
                    'processo_id' => null,
                ]);
            }
        }
    }

    protected function revertPropagation(Task $task): void
    {
        // Revert documents
        $task->documentos()->update([
            'documentable_type' => Task::class,
            'documentable_id' => $task->id,
        ]);

        // Revert piece processual
        if ($task->pecaProcessual) {
            $task->pecaProcessual->update([
                'processo_id' => null,
            ]);
        }
    }
}
