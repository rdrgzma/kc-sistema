<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // App Permissions
        $permissions = [
            'manage system',
            'manage financial',
            'view financial',
            'manage cases',
            'view cases',
            'manage clients',
            'view clients'
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::findOrCreate($perm, 'web');
        }

        // Roles
        $admin = \Spatie\Permission\Models\Role::findOrCreate('Administrador', 'web');
        $admin->syncPermissions(\Spatie\Permission\Models\Permission::all());

        $socio = \Spatie\Permission\Models\Role::findOrCreate('Sócio', 'web');
        $socio->syncPermissions([
            'manage financial', 'view financial',
            'manage cases', 'view cases',
            'manage clients', 'view clients'
        ]);

        $advogado = \Spatie\Permission\Models\Role::findOrCreate('Advogado Colaborador', 'web');
        $advogado->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients'
        ]);

        $operacional = \Spatie\Permission\Models\Role::findOrCreate('Operacional', 'web');
        $operacional->syncPermissions([
            'manage cases', 'view cases',
            'manage clients', 'view clients'
        ]);

        // Users
        $defaultPassword = \Illuminate\Support\Facades\Hash::make('password');

        $userAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userAdmin->assignRole('Administrador');

        $userSocio = \App\Models\User::firstOrCreate(
            ['email' => 'socio@admin.com'],
            [
                'name' => 'Sócio User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userSocio->assignRole('Sócio');

        $userAdvogado = \App\Models\User::firstOrCreate(
            ['email' => 'advogado@admin.com'],
            [
                'name' => 'Advogado User',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $userAdvogado->assignRole('Advogado Colaborador');

        $userOperacional = \App\Models\User::firstOrCreate(
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
