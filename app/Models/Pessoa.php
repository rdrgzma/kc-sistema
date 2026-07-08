<?php

// app/Models/Pessoa.php

namespace App\Models;

use App\Traits\HasLegacyData;
use App\Traits\LogsSystemActivity;
use App\Traits\StratifiesData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Pessoa extends Model
{
    use HasFactory, HasLegacyData, LogsActivity, StratifiesData;
    use LogsSystemActivity;

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
        'escritorio_id',
    ];

    public function escritorio(): BelongsTo
    {
        return $this->belongsTo(Escritorio::class);
    }

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

    public function pessoasJuridicas(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'pessoa_vinculos', 'pessoa_fisica_id', 'pessoa_juridica_id');
    }

    public function pessoasFisicas(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'pessoa_vinculos', 'pessoa_juridica_id', 'pessoa_fisica_id');
    }

    public function vinculos(): BelongsToMany
    {
        return $this->tipo === 'PJ' ? $this->pessoasFisicas() : $this->pessoasJuridicas();
    }
}
