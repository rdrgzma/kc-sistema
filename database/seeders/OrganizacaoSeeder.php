<?php

namespace Database\Seeders;

use App\Models\Escritorio;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizacaoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Escritórios
        $sp = Escritorio::firstOrCreate(['nome' => 'K&C Matriz São Paulo'], [
            'cnpj' => '12.345.678/0001-90',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
        ]);

        $rj = Escritorio::firstOrCreate(['nome' => 'K&C Filial Rio de Janeiro'], [
            'cnpj' => '98.765.432/0001-21',
            'cidade' => 'Rio de Janeiro',
            'uf' => 'RJ',
        ]);

        // 2. Equipes SP
        $civilSp = Equipe::firstOrCreate(['nome' => 'Departamento Cível SP', 'escritorio_id' => $sp->id], [
            'descricao' => 'Foco em litígios cíveis e estratégicos em SP'
        ]);

        $trabalhistaSp = Equipe::firstOrCreate(['nome' => 'Departamento Trabalhista SP', 'escritorio_id' => $sp->id], [
            'descricao' => 'Contencioso e consultivo trabalhista patronal'
        ]);

        // 3. Equipes RJ
        $civilRj = Equipe::firstOrCreate(['nome' => 'Departamento Cível RJ', 'escritorio_id' => $rj->id], [
            'descricao' => 'Operações cíveis na região do Rio'
        ]);

        // 4. Usuários SP
        $advSP = User::firstOrCreate(['email' => 'adv.sp@admin.com'], [
            'name' => 'Dr. Ricardo (Civil SP)',
            'password' => $password,
            'escritorio_id' => $sp->id,
            'email_verified_at' => now(),
        ]);
        $advSP->assignRole('Advogado Colaborador');
        $advSP->equipes()->sync([$civilSp->id]);

        $socioSP = User::firstOrCreate(['email' => 'socio.sp@admin.com'], [
            'name' => 'Sócio Paulo (Matriz)',
            'password' => $password,
            'escritorio_id' => $sp->id,
            'email_verified_at' => now(),
        ]);
        $socioSP->assignRole('Sócio');
        $socioSP->equipes()->sync([$civilSp->id, $trabalhistaSp->id]);

        // 5. Usuários RJ
        $advRJ = User::firstOrCreate(['email' => 'adv.rj@admin.com'], [
            'name' => 'Dra. Marina (Civil RJ)',
            'password' => $password,
            'escritorio_id' => $rj->id,
            'email_verified_at' => now(),
        ]);
        $advRJ->assignRole('Advogado Colaborador');
        $advRJ->equipes()->sync([$civilRj->id]);

        // Atualizar admin geral
        $admin = User::where('email', 'admin@admin.com')->first();
        if ($admin) {
            $admin->update(['escritorio_id' => $sp->id]);
        }
    }
}
