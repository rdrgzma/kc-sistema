<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GeneratePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate {--role= : Role to assign all generated permissions to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic CRUD permissions for all Models automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelsPath = app_path('Models');

        if (! File::isDirectory($modelsPath)) {
            $this->error("Directory {$modelsPath} does not exist.");

            return;
        }

        $models = collect(File::allFiles($modelsPath))
            ->map(function ($file) {
                return $file->getFilenameWithoutExtension();
            })
            ->reject(function ($model) {
                // Ignore pivot tables or other specific models if you want
                return in_array($model, ['EquipeUser', 'PessoaVinculo']);
            });

        $actions = ['view', 'create', 'update', 'delete', 'restore', 'force_delete'];
        $count = 0;

        $roleName = $this->option('role');
        $role = null;

        if ($roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $this->info('Generating permissions for '.$models->count().' models...');

        foreach ($models as $model) {
            $modelSnake = Str::snake(class_basename($model)); // e.g. user, processo, equipe

            foreach ($actions as $action) {
                $permissionName = "{$action}_{$modelSnake}";

                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web', // default guard
                ]);

                if ($permission->wasRecentlyCreated) {
                    $this->line("Created permission: <info>{$permissionName}</info>");
                    $count++;
                }

                if ($role) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        $this->info("Successfully generated {$count} new permissions.");

        if ($role) {
            $this->info("Assigned all permissions to role: {$roleName}");
        }
    }
}
