<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class BcbSgsService
{
    /**
     * @return array<int, array{data_referencia: string, valor: float}>
     */
    public function fetchHistorico(int $sgsCode): array
    {
        $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.{$sgsCode}/dados?formato=json";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        return $this->formatResponse($data);
    }

    /**
     * @return array<int, array{data_referencia: string, valor: float}>
     */
    public function fetchUltimoValor(int $sgsCode): array
    {
        $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.{$sgsCode}/dados/ultimos/1?formato=json";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        return $this->formatResponse($data);
    }

    private function formatResponse(array $data): array
    {
        $formatted = [];

        foreach ($data as $item) {
            $formatted[] = [
                'data_referencia' => Carbon::createFromFormat('d/m/Y', $item['data'])->format('Y-m-d'),
                'valor' => (float) $item['valor'],
            ];
        }

        return $formatted;
    }
}
