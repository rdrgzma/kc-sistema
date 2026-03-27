<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentenca extends Model
{
    protected $fillable = [
        'nome',
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
