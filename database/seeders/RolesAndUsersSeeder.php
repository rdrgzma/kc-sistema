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

        // Roles
        $admin = Role::findOrCreate('Administrador', 'web');
        $admin->syncPermissions(Permission::all());

        $socio = Role::findOrCreate('Sócio', 'web');
        $socio->syncPermissions([
            'manage financial', 'view financial',
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $advogado = Role::findOrCreate('Advogado Colaborador', 'web');
        $advogado->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        $operacional = Role::findOrCreate('Operacional', 'web');
        $operacional->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients',
        ]);

        // Users
        $defaultPassword = Hash::make('password');

        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userAdmin->assignRole('Administrador');

        $userSocio = User::firstOrCreate(
            ['email' => 'socio@admin.com'],
            [
                'name' => 'Sócio User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userSocio->assignRole('Sócio');

        $userAdvogado = User::firstOrCreate(
            ['email' => 'advogado@admin.com'],
            [
                'name' => 'Advogado User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userAdvogado->assignRole('Advogado Colaborador');

        $userOperacional = User::firstOrCreate(
            ['email' => 'operacional@admin.com'],
            [
                'name' => 'Operacional Secretária',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userOperacional->assignRole('Operacional');
    }
}
