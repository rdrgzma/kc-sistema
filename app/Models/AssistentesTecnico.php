<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistentesTecnico extends Model
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

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'assistentes_tecnico_id');
    }
}