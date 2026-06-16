<?php

namespace App\Observers;

use App\Models\Documento;
use App\Models\Processo;

class DocumentoObserver
{
    public function created(Documento $documento): void
    {
        if ($documento->documentable_type === Processo::class && $documento->documentable_id) {
            $documento->documentable->timelineEvents()->create([
                'tipo' => 'A', // Administrativo
                'descricao' => "Novo documento anexado: {$documento->nome_arquivo}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function updated(Documento $documento): void
    {
        if ($documento->documentable_type === Processo::class && $documento->documentable_id) {
            if ($documento->wasChanged('peca_processual_id')) {
                if ($documento->peca_processual_id) {
                    $peca = $documento->pecaProcessual;
                    $tipoLabel = $peca?->tipo_peca ? $peca->tipo_peca->getLabel() : 'Peça';
                    $documento->documentable->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Documento {$documento->nome_arquivo} associado à peça: {$tipoLabel}.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    $documento->documentable->timelineEvents()->create([
                        'tipo' => 'A',
                        'descricao' => "Documento {$documento->nome_arquivo} desassociado da peça.",
                        'data_evento' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }
            } else {
                $documento->documentable->timelineEvents()->create([
                    'tipo' => 'A',
                    'descricao' => "Documento editado: {$documento->nome_arquivo}.",
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }
}
