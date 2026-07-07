<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\EquipeObserver;

#[ObservedBy(EquipeObserver::class)]
class Equipe extends Model
{
    use LogsSystemActivity;

    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'escritorio_id'];

    public function escritorio(): BelongsTo
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(EquipeUser::class);
    }
}
