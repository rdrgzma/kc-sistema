<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // App Permissions
        $permissions = [
            'manage system',
            'manage financial',
            'view financial',
            'manage cases',
            'view cases',
            'manage clients',
            'view clients',
            'visualizar todas tarefas',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Base Roles (from PRD/existing system)
        $adminRole = Role::findOrCreate('Administrador', 'web');
        $adminRole->syncPermissions(Permission::all());

        $socioRole = Role::findOrCreate('Sócio', 'web');
        $socioRole->syncPermissions([
            'manage financial', 'view financial',
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $advogadoRole = Role::findOrCreate('Advogado Colaborador', 'web');
        $advogadoRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $operacionalRole = Role::findOrCreate('Operacional', 'web');
        $operacionalRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $processosRole = Role::findOrCreate('Processos', 'web');
        $processosRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
            'visualizar todas tarefas',
        ]);

        $grRole = Role::findOrCreate('GR', 'web');
        $grRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        // New Roles (requested for development and refactoring)
        $newAdminRole = Role::findOrCreate('admin', 'web');
        $newAdminRole->syncPermissions(Permission::all());

        $newSocioRole = Role::findOrCreate('socio', 'web');
        $newSocioRole->syncPermissions([
            'manage financial', 'view financial',
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $newProcessosRole = Role::findOrCreate('equipe_processos', 'web');
        $newProcessosRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
            'visualizar todas tarefas',
        ]);

        $newGrRole = Role::findOrCreate('equipe_gr', 'web');
        $newGrRole->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        // Default password for seeded users
        $defaultPassword = Hash::make('password');

        // Create user for Admin profile
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Admin Teste',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userAdmin->syncRoles(['admin', 'Administrador']);

        // Create user for Socio profile
        $userSocio = User::firstOrCreate(
            ['email' => 'socio@teste.com'],
            [
                'name' => 'Sócio Teste',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userSocio->syncRoles(['socio', 'Sócio']);

        // Create user for Equipe Processos profile
        $userProcessos = User::firstOrCreate(
            ['email' => 'processos@teste.com'],
            [
                'name' => 'Equipe Processos Teste',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userProcessos->syncRoles(['equipe_processos', 'Processos']);

        // Create user for Equipe GR profile
        $userGr = User::firstOrCreate(
            ['email' => 'gr@teste.com'],
            [
                'name' => 'Equipe GR Teste',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userGr->syncRoles(['equipe_gr', 'GR']);

        // Keep legacy seeder users if needed, or assign them
        $legacyAdmin = User::where('email', 'admin@admin.com')->first();
        if ($legacyAdmin) {
            $legacyAdmin->syncRoles(['admin', 'Administrador']);
        }

        // Assign Admin role to the default dev user created in DatabaseSeeder
        $devUser = User::where('email', 'admin@kc-sistema.dev')->first();
        if ($devUser) {
            $devUser->syncRoles(['admin', 'Administrador']);
        }
    }
}
