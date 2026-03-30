<?php

namespace App\Policies;

use App\Models\LancamentoFinanceiro;
use App\Models\User;

class LancamentoFinanceiroPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function view(User $user, LancamentoFinanceiro $lancamentoFinanceiro): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function update(User $user, LancamentoFinanceiro $lancamentoFinanceiro): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function delete(User $user, LancamentoFinanceiro $lancamentoFinanceiro): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function restore(User $user, LancamentoFinanceiro $lancamentoFinanceiro): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function forceDelete(User $user, LancamentoFinanceiro $lancamentoFinanceiro): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }
}
