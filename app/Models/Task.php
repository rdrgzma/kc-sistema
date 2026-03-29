<?php

namespace App\Models;

use App\Enums\DurationUnit;
use App\Enums\TaskUrgency;
use App\Services\TaskDurationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bucket_id', 'taskable_id', 'taskable_type',
        'title', 'description', 'assigned_to',
        'due_date', 'duration_value', 'duration_unit', 'urgency', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'duration_value' => 'integer',
            'duration_unit' => DurationUnit::class,
            'urgency' => TaskUrgency::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Task $task) {
            $service = app(TaskDurationService::class);

            // Usamos a data de criação como base, ou o 'now()' se a tarefa for nova
            $startDate = $task->created_at ?? now();

            // CASO 1: Usuário alterou o 'due_date' manualmente no form
            if ($task->isDirty('due_date') && $task->due_date) {
                $duration = $service->calculateDuration($task->due_date, $startDate);

                $task->duration_value = $duration['value'];
                $task->duration_unit = $duration['unit'];
            }
            // CASO 2: Usuário alterou a Duração (valor ou unidade) manualmente no form
            elseif ($task->isDirty(['duration_value', 'duration_unit']) && $task->duration_value && $task->duration_unit) {
                $task->due_date = $service->calculateDueDate($task->duration_value, $task->duration_unit, $startDate);
            }
        });
    }

    // A qual Entidade esta tarefa pertence (Processo, Pessoa, etc)
    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bucket(): BelongsTo
    {
        return $this->belongsTo(Bucket::class);
    }

    // Usuário responsável
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function progressos(): HasMany
    {
        return $this->hasMany(Progresso::class)->latest();
    }

    public function comentarios(): MorphMany
    {
        return $this->morphMany(Comentario::class, 'commentable')->latest();
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable')->latest();
    }

    public function timelineEvents(): MorphMany
    {
        return $this->morphMany(TimelineEvent::class, 'timelineable')->latest();
    }
}
