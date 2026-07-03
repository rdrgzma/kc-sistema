<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'processo_id',
        'titulo',
        'data_atualizacao',
        'indexador_id',
        'parametros',
        'valor_original',
        'valor_corrigido',
        'juros_total',
        'valor_final',
    ];

    protected $casts = [
        'data_atualizacao' => 'date',
        'parametros' => 'array',
        'valor_original' => 'decimal:2',
        'valor_corrigido' => 'decimal:2',
        'juros_total' => 'decimal:2',
        'valor_final' => 'decimal:2',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function indexador(): BelongsTo
    {
        return $this->belongsTo(Indexador::class);
    }
}
