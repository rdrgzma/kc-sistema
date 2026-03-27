<?php

namespace App\Models;

use App\Traits\HasLegacyData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\TimelineEvent;
use App\Models\Documento;

class Processo extends Model
{
    use HasLegacyData;

    protected $fillable = [
        'numero_processo',
        'pessoa_id',
        'seguradora_id',
        'area_id',
        'fase_id',
        'procedimento_id',
        'sentenca_id',
        'economia_gerada',
        'perda_estimada',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function seguradora(): BelongsTo
    {
        return $this->belongsTo(Seguradora::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function fase(): BelongsTo
    {
        return $this->belongsTo(Fase::class);
    }

    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class);
    }

    public function sentenca(): BelongsTo
    {
        return $this->belongsTo(Sentenca::class);
    }
    // app/Models/Processo.php

/**
 * Define a relação polimórfica com os eventos da Timeline.
 * Como o evento usa 'timelineable', o Laravel buscará os registros 
 * onde timelineable_id é o ID do processo e timelineable_type é o model Processo.
 */
    public function timelineEvents(): MorphMany
    {
        return $this->morphMany(TimelineEvent::class, 'timelineable');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');   
    }

    public function lancamentosFinanceiros(): MorphMany
    {
        return $this->morphMany(LancamentoFinanceiro::class, 'lancamentable');
    }
}