<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class TaskEstratificadoScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasPermissionTo')) {
            return;
        }

        $hasFullAccess = false;

        try {
            $hasFullAccess = $user->hasRole('Administrador') || $user->can('visualizar todas tarefas');
        } catch (PermissionDoesNotExist) {
            $hasFullAccess = false;
        }

        if ($hasFullAccess) {
            return;
        }

        $equipesIds = $user->equipes ? $user->equipes->pluck('id') : collect([]);

        $builder->where(function ($q) use ($user, $equipesIds) {
            // A tarefa está associada diretamente ao usuário
            $q->where('assigned_to', $user->id)
              // A tarefa pertence a um Planner do próprio usuário (ele é o "autor" do planner/tarefa)
                ->orWhereHas('bucket.planner', function ($qPlanner) use ($user) {
                    $qPlanner->where('user_id', $user->id);
                })
              // A tarefa pertence a um Processo da mesma equipe
                ->orWhereHas('processo', function ($qProcesso) use ($equipesIds) {
                    $qProcesso->whereIn('equipe_id', $equipesIds);
                });
        });
    }
}
