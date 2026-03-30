<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipe extends Model
{
    protected $fillable = ['nome', 'descricao', 'escritorio_id'];

    public function escritorio(): BelongsTo
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
