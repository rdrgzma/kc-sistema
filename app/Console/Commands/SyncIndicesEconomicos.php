<?php

namespace App\Console\Commands;

use App\Models\Indexador;
use App\Models\IndexadorCotacao;
use App\Services\BcbSgsService;
use Illuminate\Console\Command;

class SyncIndicesEconomicos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indices:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza as cotações dos índices econômicos da API do BCB';

    /**
     * Execute the console command.
     */
    public function handle(BcbSgsService $bcbSgsService): int
    {
        $indexadores = Indexador::whereNotNull('codigo_sgs')->get();

        $this->info("Iniciando sincronização de {$indexadores->count()} indexadores...");

        foreach ($indexadores as $indexador) {
            $this->info("Sincronizando {$indexador->sigla} (SGS: {$indexador->codigo_sgs})...");

            $cotacoesData = $bcbSgsService->fetchHistorico($indexador->codigo_sgs);

            if (empty($cotacoesData)) {
                $this->warn("Nenhum dado encontrado para {$indexador->sigla}.");

                continue;
            }

            $now = now();
            $upsertData = [];

            // Build the accumulated index for division calculation
            // BCB SGS API returns percentages (e.g. 0.98 means 0.98%)
            $acumulado = 1.0000000000;

            foreach ($cotacoesData as $item) {
                // To avoid 0 which would break division, first element or base
                $taxa = ((float) $item['valor']) / 100.0;
                $acumulado = $acumulado * (1 + $taxa);

                $upsertData[] = [
                    'indexador_id' => $indexador->id,
                    'data_referencia' => $item['data_referencia'],
                    'valor' => $acumulado,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $chunks = array_chunk($upsertData, 1000);
            foreach ($chunks as $chunk) {
                IndexadorCotacao::upsert(
                    $chunk,
                    ['indexador_id', 'data_referencia'],
                    ['valor', 'updated_at']
                );
            }

            $this->info("Sincronizado {$indexador->sigla}: ".count($cotacoesData).' registros.');
        }

        $this->info('Sincronização concluída com sucesso!');

        return Command::SUCCESS;
    }
}
