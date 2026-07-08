<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use App\Traits\StratifiesData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaFinanceira extends Model
{
    use HasFactory, StratifiesData;
    use LogsSystemActivity;

    protected $fillable = [
        'nome',
        'tipo',
        'escritorio_id',
    ];

    public function lancamentos(): HasMany
    {
        return $this->hasMany(LancamentoFinanceiro::class);
    }
}
