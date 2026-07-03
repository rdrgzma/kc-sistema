<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabelaPraticaRegra extends Model
{
    use HasFactory;

    protected $table = 'tabela_pratica_regras';

    protected $fillable = [
        'indexador_id',
        'indexador_base_id',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function indexador(): BelongsTo
    {
        return $this->belongsTo(Indexador::class, 'indexador_id');
    }

    public function indexadorBase(): BelongsTo
    {
        return $this->belongsTo(Indexador::class, 'indexador_base_id');
    }
}
