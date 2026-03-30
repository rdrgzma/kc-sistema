<?php

namespace App\Policies;

use App\Models\RateioHonorario;
use App\Models\User;

class RateioHonorarioPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function view(User $user, RateioHonorario $rateioHonorario): bool
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

    public function update(User $user, RateioHonorario $rateioHonorario): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function delete(User $user, RateioHonorario $rateioHonorario): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function restore(User $user, RateioHonorario $rateioHonorario): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }

    public function forceDelete(User $user, RateioHonorario $rateioHonorario): bool
    {
        if ($user->hasRole('Operacional')) {
            return false;
        }

        return true;
    }
}
