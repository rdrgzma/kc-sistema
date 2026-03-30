<?php

namespace App\Traits;

use App\Models\Processo;
use Illuminate\Database\Eloquent\Builder;

trait StratifiesData
{
    public function scopeEstratificado(Builder $query): Builder
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole') || $user->hasRole('Administrador')) {
            return $query;
        }

        // Para Sócios e Operacional, trava no nível do Escritório
        if ($user->hasRole('Sócio') || $user->hasRole('Operacional')) {
            return $query->where('escritorio_id', $user->escritorio_id);
        }

        // Para Advogados Colaboradores
        if ($user->hasRole('Advogado Colaborador')) {
            $query->where('escritorio_id', $user->escritorio_id); // Trava no escritório atual

            // Se for um Processo, filtra pelas Equipes do advogado ou responsabilidade direta
            if ($query->getModel() instanceof Processo) {
                // Ensure the relationship exists and retrieve the IDs
                $equipesIds = $user->equipes ? $user->equipes->pluck('id') : collect([]);
                $query->where(function ($q) use ($user, $equipesIds) {
                    $q->whereIn('equipe_id', $equipesIds)
                        ->orWhere('responsavel_id', $user->id);
                });
            }

            return $query;
        }

        return $query;
    }
}
