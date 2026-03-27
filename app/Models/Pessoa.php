<?php

// app/Models/Pessoa.php

namespace App\Models;

use App\Traits\HasLegacyData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pessoa extends Model
{
    use HasFactory, HasLegacyData;

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

    public function interacoes(): HasMany
    {
        return $this->hasMany(Interacao::class);
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
}
