<?php

namespace Database\Seeders;

use App\Models\Equipe;
use App\Models\Escritorio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizacaoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Escritórios (Apenas RJ)
        $rj = Escritorio::firstOrCreate(['nome' => 'K&C Rio de Janeiro'], [
            'cnpj' => '98.765.432/0001-21',
            'cidade' => 'Rio de Janeiro',
            'uf' => 'RJ',
        ]);

        // 2. Equipes
        $equipeGR = Equipe::firstOrCreate([
            'escritorio_id' => $rj->id,
            'nome' => 'GR',
        ]);

        $equipeProcessos = Equipe::firstOrCreate([
            'escritorio_id' => $rj->id,
            'nome' => 'Processos',
        ]);

        $equipeSocios = Equipe::firstOrCreate([
            'escritorio_id' => $rj->id,
            'nome' => 'Sócios',
        ]);

        $equipeAdmin = Equipe::firstOrCreate([
            'escritorio_id' => $rj->id,
            'nome' => 'Administradores',
        ]);

        $equipeOperacional = Equipe::firstOrCreate([
            'escritorio_id' => $rj->id,
            'nome' => 'Operacional',
        ]);

        // 3. Usuários
        $advGR = User::firstOrCreate(['email' => 'gr@admin.com'], [
            'name' => 'Advogado GR',
            'password' => $password,
            'escritorio_id' => $rj->id,
            'email_verified_at' => now(),
        ]);
        $advGR->assignRole('Advogado Colaborador');
        $advGR->equipes()->sync([$equipeGR->id]);

        $advProcessos = User::firstOrCreate(['email' => 'processos@admin.com'], [
            'name' => 'Advogada Processos',
            'password' => $password,
            'escritorio_id' => $rj->id,
            'email_verified_at' => now(),
        ]);
        $advProcessos->assignRole('Advogado Colaborador');
        $advProcessos->equipes()->sync([$equipeProcessos->id]);

        $operacional = User::firstOrCreate(['email' => 'operacional@admin.com'], [
            'name' => 'Assistente Operacional',
            'password' => $password,
            'escritorio_id' => $rj->id,
            'email_verified_at' => now(),
        ]);
        $operacional->assignRole('Operacional');
        $operacional->equipes()->sync([$equipeOperacional->id]);

        $socio = User::firstOrCreate(['email' => 'socio@admin.com'], [
            'name' => 'Sócio Diretor',
            'password' => $password,
            'escritorio_id' => $rj->id,
            'email_verified_at' => now(),
        ]);
        $socio->assignRole('Sócio');
        $socio->equipes()->sync([$equipeSocios->id]);

        // Atualizar admin geral
        $admin = User::where('email', 'admin@admin.com')->first();
        if ($admin) {
            $admin->update(['escritorio_id' => $rj->id]);
            $admin->equipes()->sync([$equipeAdmin->id]);
        }
    }
}
