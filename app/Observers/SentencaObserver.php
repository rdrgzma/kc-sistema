<?php

namespace App\Observers;

use App\Models\Sentenca;

class SentencaObserver
{
    public function updated(Sentenca $sentenca): void
    {
        // When a decision is updated, log it on all linked processos
        foreach ($sentenca->processos as $processo) {
            $processo->timelineEvents()->create([
                'tipo' => 'J', // Jurídico
                'descricao' => "Decisão editada: {$sentenca->nome}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
