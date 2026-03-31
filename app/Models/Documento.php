<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

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

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->caminho);
    }
}
