<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguradora extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
