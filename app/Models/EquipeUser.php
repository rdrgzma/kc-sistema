<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Equipe;

class EquipeUser extends Pivot
{
    use LogsSystemActivity;

    protected static function booted()
    {
        static::created(function (EquipeUser $pivot) {
            $user = User::find($pivot->user_id);
            if ($user) {
                $roleName = 'equipe_' . $pivot->equipe_id;
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                $user->assignRole($roleName);
            }
        });

        static::deleted(function (EquipeUser $pivot) {
            $user = User::find($pivot->user_id);
            if ($user) {
                $roleName = 'equipe_' . $pivot->equipe_id;
                if (\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
                    $user->removeRole($roleName);
                }
            }
        });
    }
}
