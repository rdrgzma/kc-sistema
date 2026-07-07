<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use App\Enums\ClassificacaoDecisao;
use App\Enums\StatusFinanceiroDecisao;
use App\Observers\SentencaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(SentencaObserver::class)]
class Sentenca extends Model
{
    use LogsSystemActivity;

    use HasFactory;

    protected $fillable = [
        'nome',
        'classificacao',
        'tipo_decisao',
        'valor_economia',
        'valor_perda',
        'status_financeiro',
    ];

    protected $attributes = [
        'valor_economia' => 0.00,
        'valor_perda' => 0.00,
    ];

    protected function casts(): array
    {
        return [
            'classificacao' => ClassificacaoDecisao::class,
            'status_financeiro' => StatusFinanceiroDecisao::class,
            'valor_economia' => 'decimal:2',
            'valor_perda' => 'decimal:2',
        ];
    }

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
