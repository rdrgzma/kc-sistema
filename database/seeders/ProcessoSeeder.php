<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\CategoriaFinanceira;
use App\Models\Fase;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Processo;
use App\Models\Seguradora;
use App\Models\Sentenca;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProcessoSeeder extends Seeder
{
    public function run(): void
    {
        $processos = [
            [
                'numero_processo' => '5001234-12.2024.8.21.0001',
                'pessoa_cpf' => '123.456.789-00',
                'seguradora' => 'Porto Seguro',
                'area' => 'Direito Previdenciário',
                'fase' => 'Conhecimento - 1º Grau',
                'procedimento' => 'Ordinário',
                'sentenca' => 'Aguardando Julgamento',
                'economia_gerada' => 45000.00,
                'perda_estimada' => 12000.00,
                'andamentos' => [
                    ['tipo' => 'J', 'descricao' => 'Petição inicial protocolada com todos os documentos.', 'data_evento' => '-60 days'],
                    ['tipo' => 'A', 'descricao' => 'Citação realizada via Oficial de Justiça.', 'data_evento' => '-45 days'],
                    ['tipo' => 'J', 'descricao' => 'Contestação da ré apresentada no prazo.', 'data_evento' => '-30 days'],
                    ['tipo' => 'A', 'descricao' => 'Audiência de instrução e julgamento agendada.', 'data_evento' => '+15 days'],
                ],
                'lancamentos' => [
                    ['descricao' => 'Honorários Contratuais', 'valor' => 5000.00, 'tipo' => 'R', 'status' => 'pago', 'vencimento' => '-30 days'],
                    ['descricao' => 'Custas processuais iniciais', 'valor' => 800.00, 'tipo' => 'D', 'status' => 'pago', 'vencimento' => '-58 days'],
                    ['descricao' => 'Honorários de êxito (estimado)', 'valor' => 9000.00, 'tipo' => 'R', 'status' => 'pendente', 'vencimento' => '+90 days'],
                ],
                'interacoes' => [
                    ['tipo' => 'whatsapp', 'assunto' => 'Envio de documentos faltantes', 'descricao' => 'Cliente enviou cópia do histórico de crédito via WhatsApp.', 'status' => 'realizada', 'data' => '-55 days'],
                    ['tipo' => 'telefone', 'assunto' => 'Atualização sobre andamento', 'descricao' => 'Ligação para informar sobre a contestação recebida.', 'status' => 'realizada', 'data' => '-28 days'],
                ],
            ],
            [
                'numero_processo' => '0012987-45.2023.5.04.0028',
                'pessoa_cpf' => '987.654.321-00',
                'seguradora' => 'SulAmérica',
                'area' => 'Direito Trabalhista',
                'fase' => 'Recurso - 2º Grau',
                'procedimento' => 'Sumaríssimo',
                'sentenca' => 'Parcialmente Procedente',
                'economia_gerada' => 78000.00,
                'perda_estimada' => 32000.00,
                'andamentos' => [
                    ['tipo' => 'J', 'descricao' => 'Sentença de 1º grau prolatada - parcialmente procedente.', 'data_evento' => '-90 days'],
                    ['tipo' => 'J', 'descricao' => 'Recurso ordinário interposto pelo reclamante.', 'data_evento' => '-60 days'],
                    ['tipo' => 'A', 'descricao' => 'Processo encaminhado ao TRT para julgamento do recurso.', 'data_evento' => '-20 days'],
                ],
                'lancamentos' => [
                    ['descricao' => 'Honorários 1ª fase', 'valor' => 8000.00, 'tipo' => 'R', 'status' => 'pago', 'vencimento' => '-80 days'],
                    ['descricao' => 'Depósito recursal', 'valor' => 4200.00, 'tipo' => 'D', 'status' => 'pago', 'vencimento' => '-58 days'],
                ],
                'interacoes' => [
                    ['tipo' => 'reuniao', 'assunto' => 'Reunião de alinhamento estratégico', 'descricao' => 'Apresentada estratégia recursal para o cliente.', 'status' => 'realizada', 'data' => '-55 days'],
                    ['tipo' => 'email', 'assunto' => 'Envio de cópia do recurso', 'descricao' => 'Enviada cópia completa das razões recursais para aprovação.', 'status' => 'realizada', 'data' => '-58 days'],
                ],
            ],
            [
                'numero_processo' => '7009876-00.2025.8.21.0015',
                'pessoa_cpf' => '12.345.678/0001-99',
                'seguradora' => 'Bradesco Seguros',
                'area' => 'Direito Civil',
                'fase' => 'Execução',
                'procedimento' => 'Ordinário',
                'sentenca' => 'Procedente',
                'economia_gerada' => 230000.00,
                'perda_estimada' => 0.00,
                'andamentos' => [
                    ['tipo' => 'J', 'descricao' => 'Trânsito em julgado confirmado.', 'data_evento' => '-120 days'],
                    ['tipo' => 'J', 'descricao' => 'Petição de cumprimento de sentença protocolada.', 'data_evento' => '-90 days'],
                    ['tipo' => 'F', 'descricao' => 'Bloqueio de ativos via SISBAJUD realizado (R$ 230.000,00).', 'data_evento' => '-14 days'],
                ],
                'lancamentos' => [
                    ['descricao' => 'Honorários contratuais fase conhecimento', 'valor' => 18000.00, 'tipo' => 'R', 'status' => 'pago', 'vencimento' => '-100 days'],
                    ['descricao' => 'Honorários de êxito (23%)', 'valor' => 52900.00, 'tipo' => 'R', 'status' => 'pendente', 'vencimento' => '+30 days'],
                ],
                'interacoes' => [
                    ['tipo' => 'presencial', 'assunto' => 'Assinatura de procuração para execução', 'descricao' => 'Cliente compareceu ao escritório para assinar novos poderes.', 'status' => 'realizada', 'data' => '-88 days'],
                ],
            ],
        ];

        foreach ($processos as $dados) {
            $pessoa = Pessoa::where('cpf_cnpj', $dados['pessoa_cpf'])->first();
            $seguradora = Seguradora::where('nome', $dados['seguradora'])->first();
            $area = Area::where('nome', $dados['area'])->first();
            $fase = Fase::where('nome', $dados['fase'])->first();
            $procedimento = Procedimento::where('nome', $dados['procedimento'])->first();
            $sentenca = Sentenca::where('nome', $dados['sentenca'])->first();

            if (! $pessoa) {
                continue;
            }

            $processo = Processo::firstOrCreate(
                ['numero_processo' => $dados['numero_processo']],
                [
                    'pessoa_id' => $pessoa->id,
                    'seguradora_id' => $seguradora?->id,
                    'area_id' => $area?->id,
                    'fase_id' => $fase?->id,
                    'procedimento_id' => $procedimento?->id,
                    'sentenca_id' => $sentenca?->id,
                    'economia_gerada' => $dados['economia_gerada'],
                    'perda_estimada' => $dados['perda_estimada'],
                ]
            );

            // Andamentos na timeline
            if ($processo->wasRecentlyCreated) {
                foreach ($dados['andamentos'] as $andamento) {
                    $processo->timelineEvents()->create([
                        'tipo' => $andamento['tipo'],
                        'descricao' => $andamento['descricao'],
                        'data_evento' => now()->modify($andamento['data_evento']),
                        'user_id' => null,
                    ]);
                }

                // Lançamentos financeiros
                foreach ($dados['lancamentos'] as $lanc) {
                    $tipoMapped = match ($lanc['tipo']) {
                        'R' => 'receita',
                        'D' => 'despesa',
                        default => $lanc['tipo']
                    };

                    $processo->lancamentosFinanceiros()->create([
                        'descricao' => $lanc['descricao'],
                        'valor' => $lanc['valor'],
                        'tipo' => $tipoMapped,
                        'status' => $lanc['status'],
                        'data_vencimento' => Carbon::parse($lanc['vencimento'])->toDateString(),
                        'user_id' => User::first()?->id,
                        'escritorio_id' => $pessoa->escritorio_id,
                        'categoria_financeira_id' => CategoriaFinanceira::where('escritorio_id', $pessoa->escritorio_id)->inRandomOrder()->first()?->id,
                    ]);
                }

                // Interações
                foreach ($dados['interacoes'] as $int) {
                    $processo->interacoes()->create([
                        'tipo' => $int['tipo'],
                        'assunto' => $int['assunto'],
                        'descricao' => $int['descricao'],
                        'status' => $int['status'],
                        'data_interacao' => now()->modify($int['data']),
                        'user_id' => null,
                    ]);
                }
            }
        }
    }
}
