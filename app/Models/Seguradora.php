<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguradora extends Model
{
    protected $fillable = [
        'nome',
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
