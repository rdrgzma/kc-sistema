<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidade extends Model
{
    protected $fillable = ['nome'];

    public function peritos(): HasMany
    {
        return $this->hasMany(Perito::class);
    }

    public function assistentes(): HasMany
    {
        return $this->hasMany(AssistenteTecnico::class);
    }
}
