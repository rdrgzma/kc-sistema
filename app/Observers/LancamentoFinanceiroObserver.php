<?php

namespace App\Observers;

use App\Models\LancamentoFinanceiro;
use App\Models\Processo;

class LancamentoFinanceiroObserver
{
    public function created(LancamentoFinanceiro $lancamento): void
    {
        if ($lancamento->lancamentable_type === Processo::class && $lancamento->lancamentable_id) {
            $descricao = $lancamento->descricao ?? 'Sem descrição';
            $valorFormatado = number_format((float) $lancamento->valor, 2, ',', '.');

            $lancamento->lancamentable->timelineEvents()->create([
                'tipo' => 'F',
                'descricao' => "Novo lançamento financeiro registrado: {$descricao} no valor de R$ {$valorFormatado}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
