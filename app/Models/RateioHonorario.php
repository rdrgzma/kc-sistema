<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateioHonorario extends Model
{
    protected $fillable = [
        'lancamento_financeiro_id',
        'user_id',
        'valor',
        'percentual',
        'tipo_rateio',
    ];

    public function lancamentoFinanceiro(): BelongsTo
    {
        return $this->belongsTo(LancamentoFinanceiro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
