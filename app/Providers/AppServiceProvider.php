<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Task::observe(TaskObserver::class);

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // O Administrador tem acesso total a tudo
            if ($user->hasRole('Administrador')) {
                return true;
            }

            // O Sócio tem permissão total para qualquer ação de exclusão/restauração por padrão
            if (in_array($ability, ['delete', 'forceDelete', 'deleteAny', 'forceDeleteAny', 'restore', 'restoreAny'])) {
                if ($user->hasRole('Sócio')) {
                    return true;
                }
            }
            
            return null; // Delega para as Policies ou permissões específicas
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
