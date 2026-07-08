<?php

namespace App\Models;

use App\Observers\PecaProcessualObserver;
use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(PecaProcessualObserver::class)]
class PecaProcessual extends Model
{
    use HasFactory;
    use LogsSystemActivity;

    protected $fillable = [
        'processo_id',
        'autor_id',
        'task_id',
        'tipo_peca_id',
        'data_producao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [

            'data_producao' => 'date',
        ];
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function tipoPeca(): BelongsTo
    {
        return $this->belongsTo(TipoPeca::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }
}
