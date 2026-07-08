<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPeca extends Model
{
    use HasFactory;
    use LogsSystemActivity;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function pecasProcessuais(): HasMany
    {
        return $this->hasMany(PecaProcessual::class, 'tipo_peca_id');
    }
}
