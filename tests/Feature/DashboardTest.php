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

test('users with equipe_gr role are redirected from dashboard and get correct sidebar restrictions on productivity dashboard', function () {
    $role = Role::create(['name' => 'equipe_gr']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    // Should redirect to productivity dashboard
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('dashboard.produtividade'));

    // Check sidebar visibility rules on the productivity page
    $response = $this->get(route('dashboard.produtividade'));
    $response->assertOk();

    // Should NOT see Processos, Cálculos, and Deslocamentos links in the sidebar
    $response->assertDontSee(route('processos.index'));
    $response->assertDontSee(route('calculos.index'));
    $response->assertDontSee(route('dashboard.produtividade-deslocamentos'));

    // Should see Clientes and Agenda links
    $response->assertSee(route('pessoas.index'));
    $response->assertSee(route('agenda.index'));
    $response->assertSee(route('minhas-tarefas'));

    // Should see Produtividade GR link
    $response->assertSee(route('dashboard.produtividade-gr'));
});

test('users with Processos role are redirected from dashboard to productivity dashboard', function () {
    $role = Role::create(['name' => 'Processos']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    // Should redirect to productivity dashboard
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('dashboard.produtividade'));
});

test('users with admin or socio roles are not redirected from dashboard and can see everything', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    // Should see Processos, Cálculos, Clientes, Agenda
    $response->assertSee(route('processos.index'));
    $response->assertSee(route('pessoas.index'));
    $response->assertSee(route('calculos.index'));
    $response->assertSee(route('agenda.index'));
    $response->assertSee(route('minhas-tarefas'));
});

test('regular users without permissions are not redirected and do not see Produtividade GR in sidebar', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    // Should NOT see Produtividade GR
    $response->assertDontSee(route('dashboard.produtividade-gr'));
});
