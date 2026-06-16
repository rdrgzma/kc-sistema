<?php

namespace App\Models;

use App\Observers\DocumentoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(DocumentoObserver::class)]
class Documento extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'pasta_id',
        'nome_arquivo',
        'caminho',
        'extensao',
        'tamanho',
        'categoria',
        'user_id',
        'peca_processual_id',
        'task_id',
    ];

    public function pasta(): BelongsTo
    {
        return $this->belongsTo(Pasta::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pecaProcessual(): BelongsTo
    {
        return $this->belongsTo(PecaProcessual::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->caminho);
    }
}
