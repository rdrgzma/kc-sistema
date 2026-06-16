<?php

namespace App\Observers;

use App\Models\Fase;
use App\Models\Processo;
use App\Services\FinanceiroService;

class ProcessoObserver
{
    public function __construct(
        protected FinanceiroService $financeiroService
    ) {}

    public function updated(Processo $processo): void
    {
        if ($processo->wasChanged('fase_id')) {
            $faseAntigaId = $processo->getOriginal('fase_id');
            $faseNovaId = $processo->fase_id;

            $faseAntigaNome = $faseAntigaId ? Fase::find($faseAntigaId)?->nome : 'Nenhum';
            $faseNovaNome = $faseNovaId ? Fase::find($faseNovaId)?->nome : 'Nenhum';

            $processo->timelineEvents()->create([
                'tipo' => 'J',
                'descricao' => "Fase do processo alterada de {$faseAntigaNome} para {$faseNovaNome}.",
                'data_evento' => now(),
                'user_id' => auth()->id(),
            ]);

            if ($faseNovaId) {
                $novaFase = Fase::find($faseNovaId);

                if ($novaFase && $novaFase->valor_custa_padrao > 0) {
                    $this->financeiroService->gerarCustaAutomatica(
                        $processo,
                        "Custas automáticas da fase: {$novaFase->nome}",
                        (float) $novaFase->valor_custa_padrao,
                        now()->addDays(5)->format('Y-m-d')
                    );
                }
            }
        }

        if ($processo->wasChanged('sentenca_id')) {
            if ($processo->sentenca_id) {
                $sentenca = $processo->sentenca;
                $nomeDecisao = $sentenca ? $sentenca->nome : 'Nova Decisão';
                $processo->timelineEvents()->create([
                    'tipo' => 'J',
                    'descricao' => "Nova decisão registrada: {$nomeDecisao}.",
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            } else {
                $processo->timelineEvents()->create([
                    'tipo' => 'J',
                    'descricao' => 'Decisão removida do processo.',
                    'data_evento' => now(),
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }
}
