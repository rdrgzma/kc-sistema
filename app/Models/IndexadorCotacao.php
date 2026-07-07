<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndexadorCotacao extends Model
{
    use LogsSystemActivity;

    use HasFactory;

    protected $table = 'indexador_cotacoes';

    protected $fillable = [
        'indexador_id',
        'data_referencia',
        'valor',
    ];

    protected $casts = [
        'data_referencia' => 'date',
        'valor' => 'decimal:6',
    ];

    public function indexador(): BelongsTo
    {
        return $this->belongsTo(Indexador::class, 'indexador_id');
    }
}
