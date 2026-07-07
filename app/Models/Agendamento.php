<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Agendamento extends Model
{
    use LogsSystemActivity;

    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Compromisso agendado',
                'updated' => 'Compromisso atualizado',
                'deleted' => 'Compromisso removido',
                default => "Compromisso {$eventName}",
            });
    }

    protected $fillable = [
        'user_id',
        'escritorio_id',
        'processo_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function escritorio(): BelongsTo
    {
        return $this->belongsTo(Escritorio::class);
    }
}
