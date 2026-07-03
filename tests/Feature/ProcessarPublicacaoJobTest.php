<?php

use App\DTOs\PublicacaoDTO;
use App\Jobs\ProcessarPublicacaoJob;
use App\Models\Bucket;
use App\Models\Planner;
use App\Models\Processo;
use Carbon\Carbon;

it('processa publicação criando andamento e tarefa no kanban', function () {
    // Arrange
    $this->artisan('db:seed');
    $processo = Processo::first();
    
    $dataPublicacao = Carbon::parse('2023-10-10 10:00:00');
    $dto = new PublicacaoDTO(
        processoId: $processo->id,
        textoPublicacao: 'Publicação de teste',
        dataPublicacao: $dataPublicacao
    );

    // Act
    ProcessarPublicacaoJob::dispatchSync($dto);

    // Assert
    // Verifica TimelineEvent
    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => $processo->getMorphClass(),
        'timelineable_id' => $processo->id,
        'tipo' => 'J',
        'descricao' => 'Publicação Oficial: Publicação de teste',
        'data_evento' => '2023-10-10 10:00:00',
    ]);

    // Verifica Task
    $this->assertDatabaseHas('tasks', [
        'processo_id' => $processo->id,
        'title' => 'Analisar Nova Publicação',
        'description' => 'Publicação de teste',
        'due_date' => $dataPublicacao->copy()->addWeekdays(2)->format('Y-m-d H:i:s'),
    ]);
});
