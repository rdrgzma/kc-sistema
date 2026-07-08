<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indexador extends Model
{
    use HasFactory;
    use LogsSystemActivity;

    protected $table = 'indexadores';

    protected $fillable = [
        'categoria',
        'nome',
        'sigla',
        'codigo_sgs',
        'tipo',
        'fonte',
        'is_composto',
    ];

    protected $casts = [
        'is_composto' => 'boolean',
    ];

    public function cotacoes(): HasMany
    {
        return $this->hasMany(IndexadorCotacao::class, 'indexador_id');
    }

    public function regras(): HasMany
    {
        return $this->hasMany(TabelaPraticaRegra::class, 'indexador_id');
    }
}
