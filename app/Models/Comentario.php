<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use App\Observers\ComentarioObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ComentarioObserver::class)]
class Comentario extends Model
{
    use LogsSystemActivity;

    protected $table = 'comentarios';

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'user_id',
        'content',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
