<?php

namespace App\Models;

use App\Traits\HasLegacyData;
use App\Traits\HasTasks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Processo extends Model
{
    use HasLegacyData, HasTasks, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Processo cadastrado',
                'updated' => 'Processo atualizado',
                'deleted' => 'Processo removido',
                default => "Processo {$eventName}",
            });
    }

    protected $fillable = [
        'numero_processo',
        'pessoa_id',
        'seguradora_id',
        'area_id',
        'fase_id',
        'fase_recursal_id',
        'procedimento_id',
        'sentenca_id',
        'responsavel_id',
        'perito_id',
        'assistentes_tecnico_id',
        'economia_gerada',
        'perda_estimada',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function seguradora(): BelongsTo
    {
        return $this->belongsTo(Seguradora::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function fase(): BelongsTo
    {
        return $this->belongsTo(Fase::class);
    }

    public function faseRecursal(): BelongsTo
    {
        // if you later create a FaseRecursal model/table, change this accordingly
        return $this->belongsTo(Fase::class, 'fase_recursal_id');
    }

    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class);
    }

    public function sentenca(): BelongsTo
    {
        return $this->belongsTo(Sentenca::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function perito(): BelongsTo
    {
        return $this->belongsTo(Perito::class, 'perito_id');
    }

    public function assistenteTecnico(): BelongsTo
    {
        return $this->belongsTo(AssistentesTecnico::class, 'assistentes_tecnico_id');
    }

    public function timelineEvents(): MorphMany
    {
        return $this->morphMany(TimelineEvent::class, 'timelineable');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    public function lancamentosFinanceiros(): MorphMany
    {
        return $this->morphMany(LancamentoFinanceiro::class, 'lancamentable');
    }

    public function interacoes(): MorphMany
    {
        return $this->morphMany(Interacao::class, 'interactable');
    }

    public function getTodosDocumentosAttribute(): Collection
    {
        // 1. Documentos diretos do processo
        $diretos = $this->documentos;

        // 2. Documentos das tarefas atreladas a este processo
        $tarefasIds = $this->tasks()->pluck('id');
        $dasTarefas = Documento::where('documentable_type', Task::class)
            ->whereIn('documentable_id', $tarefasIds)
            ->get();

        // Une, remove duplicados (caso existam) e ordena por data
        return $diretos->concat($dasTarefas)
            ->unique('id')
            ->sortByDesc('created_at')
            ->values();
    }
}
