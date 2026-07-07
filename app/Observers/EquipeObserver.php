<?php

namespace App\Observers;

use App\Models\Equipe;
use Spatie\Permission\Models\Role;

class EquipeObserver
{
    /**
     * Handle the Equipe "created" event.
     */
    public function created(Equipe $equipe): void
    {
        Role::firstOrCreate(['name' => 'equipe_' . $equipe->id]);
    }

    /**
     * Handle the Equipe "deleted" event.
     */
    public function deleted(Equipe $equipe): void
    {
        $role = Role::where('name', 'equipe_' . $equipe->id)->first();
        if ($role) {
            $role->delete();
        }
    }
}
