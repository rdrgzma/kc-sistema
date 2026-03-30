<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escritorio extends Model
{
    protected $fillable = ['nome', 'cnpj', 'cidade', 'uf'];

    public function equipes(): HasMany
    {
        return $this->hasMany(Equipe::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
