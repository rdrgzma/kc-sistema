<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nome_arquivo',
        'caminho',
        'extensao',
        'tamanho',
        'categoria',
        'user_id',
    ];
    public function documentable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getUrlAttribute()
    {
        return \Illuminate\Support\Facades\Storage::url($this->caminho);
    }
}
