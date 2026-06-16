<?php

namespace App\Observers;

use App\Models\Interacao;
use App\Models\Processo;

class InteracaoObserver
{
    public function created(Interacao $interacao): void
    {
        if ($interacao->interactable_type === Processo::class && $interacao->interactable_id) {
            $assunto = $interacao->assunto ?? 'Sem assunto';
            $interacao->interactable->timelineEvents()->create([
                'tipo' => 'A', // Administrativo
                'descricao' => "Novo atendimento registrado: {$assunto}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function updated(Interacao $interacao): void
    {
        if ($interacao->interactable_type === Processo::class && $interacao->interactable_id) {
            $assunto = $interacao->assunto ?? 'Sem assunto';
            $interacao->interactable->timelineEvents()->create([
                'tipo' => 'A',
                'descricao' => "Atendimento editado: {$assunto}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
