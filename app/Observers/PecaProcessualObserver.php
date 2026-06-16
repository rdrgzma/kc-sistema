<?php

namespace App\Observers;

use App\Models\PecaProcessual;

class PecaProcessualObserver
{
    public function created(PecaProcessual $peca): void
    {
        if ($peca->processo_id) {
            $tipoLabel = $peca->tipo_peca ? $peca->tipo_peca->getLabel() : 'Peça';
            $peca->processo->timelineEvents()->create([
                'tipo' => 'J', // Jurídico
                'descricao' => "Nova peça processual registrada: {$tipoLabel}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function updated(PecaProcessual $peca): void
    {
        if ($peca->processo_id) {
            $tipoLabel = $peca->tipo_peca ? $peca->tipo_peca->getLabel() : 'Peça';
            $peca->processo->timelineEvents()->create([
                'tipo' => 'J',
                'descricao' => "Peça processual editada: {$tipoLabel}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
