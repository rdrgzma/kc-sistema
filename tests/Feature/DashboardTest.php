<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('users with equipe_gr role do not see restricted menu items but see Produtividade GR in sidebar', function () {
    $role = Role::create(['name' => 'equipe_gr']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    // Should NOT see Processos, Cálculos, and Deslocamentos
    $response->assertDontSee('Processos');
    $response->assertDontSee('Cálculos');
    $response->assertDontSee('Deslocamentos');

    // Should see Clientes and Agenda
    $response->assertSee('Clientes');
    $response->assertSee('Agenda');

    // Should see Produtividade GR
    $response->assertSee('Produtividade GR');
});

test('users with admin or socio roles can see processes, calculations and Produtividade GR in sidebar', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    // Should see Processos, Cálculos, Clientes, Agenda and Produtividade GR
    $response->assertSee('Processos');
    $response->assertSee('Clientes');
    $response->assertSee('Cálculos');
    $response->assertSee('Agenda');
    $response->assertSee('Produtividade GR');
});

test('regular users without permissions do not see Produtividade GR in sidebar', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    // Should NOT see Produtividade GR
    $response->assertDontSee('Produtividade GR');
});
