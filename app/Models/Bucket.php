<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bucket extends Model
{
    protected $fillable = ['planner_id', 'name', 'color', 'sort'];

    public function planner(): BelongsTo
    {
        return $this->belongsTo(Planner::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('sort');
    }
}
