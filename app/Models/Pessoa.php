<?php

// app/Models/Pessoa.php

namespace App\Models;

use App\Traits\HasLegacyData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Pessoa extends Model
{
    use HasFactory, HasLegacyData, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logExcept(['legacy_id', 'legacy_table'])
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Cliente cadastrado',
                'updated' => 'Dados do cliente atualizados',
                'deleted' => 'Cliente removido',
                default => "Cliente {$eventName}",
            });
    }

    protected $fillable = [
        'tipo',
        'nome_razao',
        'cpf_cnpj',
        'email',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    // O Antigravity já pode deixar os relacionamentos declarados para quando criarmos as classes

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class);
    }

    // Relação Polimórfica para o Módulo de Documentos (GR Avançado)
    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    public function lancamentosFinanceiros(): MorphMany
    {
        return $this->morphMany(LancamentoFinanceiro::class, 'lancamentable');
    }

    public function interacoes(): MorphMany
    {
        return $this->morphMany(Interacao::class, 'interactable');
    }
}
