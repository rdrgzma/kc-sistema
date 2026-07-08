<?php

namespace App\Models;

use App\Enums\AcaoGR;
use App\Enums\DurationUnit;
use App\Enums\TaskUrgency;
use App\Observers\TaskObserver;
use App\Services\TaskDurationService;
use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use LogsSystemActivity;
    use SoftDeletes;

    protected $fillable = [
        'bucket_id', 'taskable_id', 'taskable_type',
        'title', 'description', 'assigned_to',
        'due_date', 'duration_value', 'duration_unit', 'urgency', 'sort',
        'pessoa_id', 'processo_id', 'inicios_count', 'conclusoes_count',
        'acao_gr', 'data_solicitacao', 'data_envio', 'repeticoes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'duration_value' => 'integer',
            'duration_unit' => DurationUnit::class,
            'urgency' => TaskUrgency::class,
            'acao_gr' => AcaoGR::class,
            'data_solicitacao' => 'date',
            'data_envio' => 'date',
            'repeticoes' => 'integer',
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

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class)->latest();
    }

    public function timelineEvents(): MorphMany
    {
        return $this->morphMany(TimelineEvent::class, 'timelineable')->latest();
    }

    public function pecaProcessual(): HasOne
    {
        return $this->hasOne(PecaProcessual::class);
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function scopeEstratificado(Builder $query): Builder
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasPermissionTo')) {
            return $query;
        }

        $hasFullAccess = false;

        try {
            $hasFullAccess = $user->hasRole('Administrador') || $user->can('visualizar todas tarefas');
        } catch (PermissionDoesNotExist) {
            $hasFullAccess = false;
        }

        if ($hasFullAccess) {
            return $query;
        }

        $equipesIds = $user->equipes ? $user->equipes->pluck('id') : collect([]);

        return $query->where(function ($q) use ($user, $equipesIds) {
            $q->where('assigned_to', $user->id)
                ->orWhereHas('bucket.planner', function ($qPlanner) use ($user) {
                    $qPlanner->where('user_id', $user->id);
                })
                ->orWhereHas('processo', function ($qProcesso) use ($equipesIds) {
                    $qProcesso->whereIn('equipe_id', $equipesIds);
                });
        });
    }
}
