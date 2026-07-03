<?php

namespace App\Jobs;

use App\DTOs\PublicacaoDTO;
use App\Services\ProcessadorPublicacaoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarPublicacaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly PublicacaoDTO $dto
    ) {}

    public function handle(ProcessadorPublicacaoService $service): void
    {
        $service->processar($this->dto);
    }
}
