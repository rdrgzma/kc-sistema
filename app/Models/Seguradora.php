<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguradora extends Model
{
    use HasFactory;
    use LogsSystemActivity;

    protected $fillable = [
        'nome',
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
