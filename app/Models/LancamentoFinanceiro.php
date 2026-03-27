<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LancamentoFinanceiro extends Model
{
    // Forçamos o nome da tabela caso a migration use o plural padrão
    protected $table = 'lancamento_financeiros';

    protected $fillable = [
        'descricao', 
        'valor', 
        'data_vencimento', 
        'data_pagamento', 
        'tipo', 
        'status', 
        'user_id'
    ];

    public function lancamentable(): MorphTo
    {
        return $this->morphTo();
    }
}
