<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planner extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'plannable_id', 'plannable_type', 'user_id'];

    public function plannable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buckets(): HasMany
    {
        return $this->hasMany(Bucket::class)->orderBy('sort');
    }

    // Helper útil para buscar todas as tarefas do planner através dos buckets
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Bucket::class);
    }
}
