<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Interacao extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipo', 'assunto', 'descricao', 'status', 'data_interacao'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Interação registrada',
                'updated' => 'Interação atualizada',
                'deleted' => 'Interação removida',
                default => "Interação {$eventName}",
            });
    }

    protected $table = 'interacaos';

    protected $fillable = [
        'tipo', 'assunto', 'descricao',
        'data_interacao', 'status', 'user_id',
    ];

    protected $casts = [
        'data_interacao' => 'datetime',
    ];

    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
