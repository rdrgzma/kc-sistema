<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário padrão de dev
        $user = User::factory()->create([
            'name'  => 'Administrador',
            'email' => 'admin@kc-sistema.dev',
        ]);

        $this->call([
            RolesAndUsersSeeder::class,
            OrganizacaoSeeder::class,
            // Lookup tables (sem dependências)
            AreaSeeder::class,
            FaseSeeder::class,
            ProcedimentoSeeder::class,
            SentencaSeeder::class,
            SeguradoraSeeder::class,
            // Entidades principais
            PessoaSeeder::class,
            // Processos + relacionados (timeline, lançamentos, interações)
            ProcessoSeeder::class,
        ]);
    }
}
