<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perito extends Model
{
    protected $fillable = ['nome', 'especialidade_id', 'user_id', 'legacy_id'];

    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relacionados (opcional): processos que referenciam este perito
    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'perito_id');
    }
}