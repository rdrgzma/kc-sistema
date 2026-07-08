<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Permission\Models\Role;

class EquipeUser extends Pivot
{
    use LogsSystemActivity;

    protected static function booted()
    {
        static::created(function (EquipeUser $pivot) {
            $user = User::find($pivot->user_id);
            if ($user) {
                $roleName = 'equipe_'.$pivot->equipe_id;
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                $user->assignRole($roleName);
            }
        });

        static::deleted(function (EquipeUser $pivot) {
            $user = User::find($pivot->user_id);
            if ($user) {
                $roleName = 'equipe_'.$pivot->equipe_id;
                if (Role::where('name', $roleName)->exists()) {
                    $user->removeRole($roleName);
                }
            }
        });
    }
}
