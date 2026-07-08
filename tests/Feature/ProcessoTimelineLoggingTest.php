<?php

use App\Enums\ClassificacaoDecisao;
use App\Enums\StatusFinanceiroDecisao;
use App\Enums\TaskUrgency;
use App\Livewire\DocumentManager;
use App\Livewire\Processo\DecisoesRelationManager;
use App\Models\Area;
use App\Models\Bucket;
use App\Models\CategoriaFinanceira;
use App\Models\Escritorio;
use App\Models\Fase;
use App\Models\PecaProcessual;
use App\Models\Pessoa;
use App\Models\Planner;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Sentenca;
use App\Models\Task;
use App\Models\TipoPeca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->escritorio = Escritorio::create(['nome' => 'K&C Advogados']);
    $this->area = Area::create(['nome' => 'Cível']);
    $this->fase = Fase::create(['nome' => 'Conhecimento']);
    $this->procedimento = Procedimento::create(['nome' => 'Comum']);
    $this->pessoa = Pessoa::factory()->create(['escritorio_id' => $this->escritorio->id]);

    $this->processo = Processo::factory()->create([
        'area_id' => $this->area->id,
        'fase_id' => $this->fase->id,
        'procedimento_id' => $this->procedimento->id,
        'escritorio_id' => $this->escritorio->id,
        'pessoa_id' => $this->pessoa->id,
        'responsavel_id' => $this->user->id,
        'sentenca_id' => null,
    ]);
});

test('creating and updating a document creates timeline events', function () {
    $documento = $this->processo->documentos()->create([
        'nome_arquivo' => 'contrato.pdf',
        'caminho' => 'docs/contrato.pdf',
        'extensao' => 'pdf',
        'tamanho' => 1024,
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Novo documento anexado: contrato.pdf.',
    ]);

    $documento->update(['nome_arquivo' => 'contrato_assinado.pdf']);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Documento editado: contrato_assinado.pdf.',
    ]);
});

test('creating and updating an interacao creates timeline events', function () {
    $interacao = $this->processo->interacoes()->create([
        'tipo' => 'reuniao',
        'assunto' => 'Alinhamento inicial',
        'descricao' => 'Discussão sobre a petição inicial',
        'data_interacao' => now(),
        'status' => 'realizada',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Novo atendimento registrado: Alinhamento inicial.',
    ]);

    $interacao->update(['assunto' => 'Alinhamento inicial atualizado']);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Atendimento editado: Alinhamento inicial atualizado.',
    ]);
});

test('creating and updating a peca processual creates timeline events', function () {
    $peca = PecaProcessual::create([
        'processo_id' => $this->processo->id,
        'autor_id' => $this->user->id,
        'tipo_peca_id' => TipoPeca::firstOrCreate(['nome' => 'Petições de Expediente'])->id,
        'data_producao' => now(),
        'observacoes' => 'Petição inicial pronta',
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Nova peça processual registrada: Petições de Expediente.',
    ]);

    $peca->update(['tipo_peca_id' => TipoPeca::firstOrCreate(['nome' => 'Contestação'])->id]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Peça processual editada: Contestação.',
    ]);
});

test('creating and updating sentenca triggers proper events', function () {
    $sentenca = Sentenca::create([
        'nome' => 'Procedente',
        'valor_economia' => 1000.00,
        'valor_perda' => 0.00,
    ]);

    // Associating sentenca to processo triggers updated event on Processo
    $this->processo->update(['sentenca_id' => $sentenca->id]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Nova decisão registrada: Procedente.',
    ]);

    // Updating sentenca itself triggers updated event on Sentenca, which updates the processo timeline
    $sentenca->update(['nome' => 'Procedente com Embargos']);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Decisão editada: Procedente com Embargos.',
    ]);
});

test('creating and updating lancamento financeiro creates timeline events', function () {
    $categoria = CategoriaFinanceira::create([
        'nome' => 'Honorários',
        'tipo' => 'receita',
        'escritorio_id' => $this->escritorio->id,
    ]);

    $lancamento = $this->processo->lancamentosFinanceiros()->create([
        'descricao' => 'Recebimento de honorários',
        'valor' => 1250.50,
        'tipo' => 'receita',
        'categoria_financeira_id' => $categoria->id,
        'status' => 'pago',
        'data_vencimento' => now()->toDateString(),
        'escritorio_id' => $this->escritorio->id,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'F',
        'descricao' => 'Novo lançamento financeiro registrado: Recebimento de honorários no valor de R$ 1.250,50.',
    ]);

    $lancamento->update(['descricao' => 'Recebimento de honorários revisados']);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'F',
        'descricao' => 'Lançamento financeiro editado: Recebimento de honorários revisados no valor de R$ 1.250,50.',
    ]);
});

test('associating and disassociating a document to a piece processual logs proper timeline events', function () {
    $documento = $this->processo->documentos()->create([
        'nome_arquivo' => 'peticao_doc.pdf',
        'caminho' => 'docs/peticao_doc.pdf',
        'extensao' => 'pdf',
        'tamanho' => 2048,
        'user_id' => $this->user->id,
    ]);

    $peca = PecaProcessual::create([
        'processo_id' => $this->processo->id,
        'autor_id' => $this->user->id,
        'tipo_peca_id' => TipoPeca::firstOrCreate(['nome' => 'Petições de Expediente'])->id,
        'data_producao' => now(),
        'observacoes' => 'Petição com doc',
    ]);

    // Associate
    $documento->update(['peca_processual_id' => $peca->id]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Documento peticao_doc.pdf associado à peça: Petições de Expediente.',
    ]);

    // Disassociate
    $documento->update(['peca_processual_id' => null]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Documento peticao_doc.pdf desassociado da peça.',
    ]);
});

test('task lifecycle and document/piece propagation to process logs timeline events', function () {
    $bucket = Bucket::create([
        'planner_id' => Planner::create([
            'name' => 'Plano de Trabalho',
            'user_id' => $this->user->id,
        ])->id,
        'name' => 'Backlog',
        'color' => '#123456',
        'sort' => 1,
    ]);

    // Create Task without process
    $task = Task::create([
        'bucket_id' => $bucket->id,
        'title' => 'Escrever petição',
        'description' => 'Tarefa inicial',
        'urgency' => TaskUrgency::NORMAL,
    ]);

    // Attach document to task
    $documento = $task->documentos()->create([
        'nome_arquivo' => 'anexo_tarefa.pdf',
        'caminho' => 'docs/anexo_tarefa.pdf',
        'extensao' => 'pdf',
        'tamanho' => 512,
        'user_id' => $this->user->id,
        'documentable_type' => Task::class,
        'documentable_id' => $task->id,
    ]);

    // Attach piece processual to task
    $peca = PecaProcessual::create([
        'processo_id' => null,
        'task_id' => $task->id,
        'autor_id' => $this->user->id,
        'tipo_peca_id' => TipoPeca::firstOrCreate(['nome' => 'Petições de Expediente'])->id,
        'data_producao' => now(),
    ]);

    // Link task to process
    $task->update(['processo_id' => $this->processo->id]);

    // Timeline event logged on process
    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Tarefa associada ao processo: Escrever petição.',
    ]);

    // Document propagated to process
    $documento->refresh();
    expect($documento->documentable_type)->toBe(Processo::class);
    expect($documento->documentable_id)->toBe($this->processo->id);

    // Piece processual propagated to process
    $peca->refresh();
    expect($peca->processo_id)->toBe($this->processo->id);

    // Unlink task from process
    $task->update(['processo_id' => null]);

    // Timeline event logged on process
    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => 'Tarefa desassociada do processo: Escrever petição.',
    ]);

    // Document reverted
    $documento->refresh();
    expect($documento->documentable_type)->toBe(Task::class);
    expect($documento->documentable_id)->toBe($task->id);

    // Piece processual reverted
    $peca->refresh();
    expect($peca->processo_id)->toBeNull();
});

test('adding, editing, and deleting comments on a task associated to a process logs timeline events', function () {
    $bucket = Bucket::create([
        'planner_id' => Planner::create([
            'name' => 'Plano de Trabalho',
            'user_id' => $this->user->id,
        ])->id,
        'name' => 'Backlog',
        'color' => '#123456',
        'sort' => 1,
    ]);

    $task = Task::create([
        'bucket_id' => $bucket->id,
        'title' => 'Analisar provas',
        'urgency' => TaskUrgency::NORMAL,
        'processo_id' => $this->processo->id,
    ]);

    // Create Comment
    $comment = $task->comentarios()->create([
        'user_id' => $this->user->id,
        'content' => 'Precisa de mais documentos',
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => "Novo comentário na tarefa 'Analisar provas'.",
    ]);

    // Update Comment
    $comment->update(['content' => 'Documentos recebidos']);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => "Comentário editado na tarefa 'Analisar provas'.",
    ]);

    // Delete Comment
    $comment->delete();

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'A',
        'descricao' => "Comentário removido da tarefa 'Analisar provas'.",
    ]);
});

test('task GED is isolated when no client or process is associated, and shared otherwise', function () {
    $bucket = Bucket::create([
        'planner_id' => Planner::create([
            'name' => 'Plano de Trabalho',
            'user_id' => $this->user->id,
        ])->id,
        'name' => 'Backlog',
        'color' => '#123456',
        'sort' => 1,
    ]);

    // Scenario 1: Isolated Task
    $task = Task::create([
        'bucket_id' => $bucket->id,
        'title' => 'Tarefa Isolada',
        'urgency' => TaskUrgency::NORMAL,
    ]);

    $component = Livewire::test(DocumentManager::class, ['model' => $task]);
    $component->assertOk();

    // Verify isolated query target
    $pastable = $component->instance()->getPastableInfo();
    expect($pastable['type'])->toBe(Task::class);
    expect($pastable['id'])->toBe($task->id);

    // Scenario 2: Task associated with Client (Pessoa)
    $task->update(['pessoa_id' => $this->pessoa->id]);
    $component = Livewire::test(DocumentManager::class, ['model' => $task]);
    $pastable = $component->instance()->getPastableInfo();
    expect($pastable['type'])->toBe(Pessoa::class);
    expect($pastable['id'])->toBe($this->pessoa->id);

    // Scenario 3: Task associated with Process (Processo)
    $task->update(['processo_id' => $this->processo->id]);
    $component = Livewire::test(DocumentManager::class, ['model' => $task]);
    $pastable = $component->instance()->getPastableInfo();
    expect($pastable['type'])->toBe(Processo::class);
    expect($pastable['id'])->toBe($this->processo->id);
});

test('decisoes relation manager actions trigger proper timeline events', function () {
    $component = Livewire::test(DecisoesRelationManager::class, ['processo' => $this->processo]);
    $component->assertOk();

    // 1. Create Decision through Action
    $component->callTableAction('create', data: [
        'nome' => 'Sentença Cível 1',
        'tipo_decisao' => 'Sentença',
        'classificacao' => ClassificacaoDecisao::FAVORAVEL->value,
        'status_financeiro' => StatusFinanceiroDecisao::SEM_PERDA_ECONOMICA->value,
        'valor_economia' => 1500.00,
        'valor_perda' => 0.00,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Nova decisão registrada: Sentença Cível 1.',
    ]);

    // Fetch the created sentenca
    $this->processo->refresh();
    $sentenca = $this->processo->sentenca;
    expect($sentenca)->not->toBeNull();

    // 2. Edit Decision through Action
    $component->callTableAction('edit', $sentenca, [
        'nome' => 'Sentença Cível 1 Modificada',
        'tipo_decisao' => 'Sentença',
        'classificacao' => ClassificacaoDecisao::FAVORAVEL->value,
        'status_financeiro' => StatusFinanceiroDecisao::SEM_PERDA_ECONOMICA->value,
        'valor_economia' => 1800.00,
        'valor_perda' => 0.00,
    ]);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Decisão editada: Sentença Cível 1 Modificada.',
    ]);

    // 3. Delete Decision through Action
    $component->callTableAction('delete', $sentenca);

    $this->assertDatabaseHas('timeline_events', [
        'timelineable_type' => Processo::class,
        'timelineable_id' => $this->processo->id,
        'tipo' => 'J',
        'descricao' => 'Decisão removida do processo.',
    ]);
});
