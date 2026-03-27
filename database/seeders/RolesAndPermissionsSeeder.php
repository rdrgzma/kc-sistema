<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa o cache de permissões antes de rodar
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Criação das Roles baseadas no PRD
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Sócio']);
        Role::create(['name' => 'Advogado Colaborador']);
        Role::create(['name' => 'Operacional']);
    }
}