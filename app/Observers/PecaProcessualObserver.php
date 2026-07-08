<?php

namespace App\Observers;

use App\Models\PecaProcessual;

class PecaProcessualObserver
{
    public function created(PecaProcessual $peca): void
    {
        if ($peca->processo_id) {
            $tipoLabel = $peca->tipoPeca ? $peca->tipoPeca->nome : 'Peça';
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
            $peca->unsetRelation('tipoPeca');
            $tipoLabel = $peca->tipoPeca ? $peca->tipoPeca->nome : 'Peça';
            $peca->processo->timelineEvents()->create([
                'tipo' => 'J',
                'descricao' => "Peça processual editada: {$tipoLabel}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
