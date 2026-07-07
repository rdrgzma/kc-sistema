<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GenerateModelPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate standard CRUD permissions for all models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelsPath = app_path('Models');
        
        if (!File::exists($modelsPath)) {
            $this->error('Directory app/Models does not exist.');
            return;
        }

        $files = File::files($modelsPath);
        $actions = ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'force_delete'];

        $count = 0;

        foreach ($files as $file) {
            $modelName = $file->getFilenameWithoutExtension();
            // Evitar criar permissões para models pivot ou outros ignorados, se necessário.
            // Converte para snake case (ex: User -> user, Task -> task)
            $modelSnake = Str::snake($modelName);
            
            foreach ($actions as $action) {
                $permissionName = "{$action}_{$modelSnake}";
                
                $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                if ($permission->wasRecentlyCreated) {
                    $this->info("Created permission: {$permissionName}");
                    $count++;
                }
            }
        }

        $this->info("Done! Created {$count} new permissions.");
    }
}
