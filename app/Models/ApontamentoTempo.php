<?php

namespace App\Models;

use App\Enums\ModalidadeAtividade;
use App\Enums\TipoAtividadeDeslocamento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApontamentoTempo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'processo_id',
        'tipo_atividade',
        'descricao',
        'modalidade',
        'local',
        'data_atividade',
        'hora_inicio',
        'hora_fim',
    ];

    protected function casts(): array
    {
        return [
            'tipo_atividade' => TipoAtividadeDeslocamento::class,
            'modalidade' => ModalidadeAtividade::class,
            'data_atividade' => 'date',
        ];
    }

    public int $tempo_deslocamento {
        get {
            $inicio = $this->hora_inicio;
            $fim = $this->hora_fim;
            if (! $inicio || ! $fim) {
                return 0;
            }

            $start = $inicio instanceof Carbon ? $inicio : Carbon::parse($inicio);
            $end = $fim instanceof Carbon ? $fim : Carbon::parse($fim);

            return (int) $start->diffInMinutes($end);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
}
