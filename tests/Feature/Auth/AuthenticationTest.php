<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Laravel\Fortify\Features;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect('/');

    $this->assertGuest();
});

test('quick login shortcuts are visible in local environment', function () {
    app()['env'] = 'local';

    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('Atalhos de Desenvolvimento')
        ->assertSee('admin@teste.com')
        ->assertSee('socio@teste.com')
        ->assertSee('processos@teste.com')
        ->assertSee('gr@teste.com');
});

test('quick login shortcuts are not visible in production environment', function () {
    app()['env'] = 'production';

    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertDontSee('Atalhos de Desenvolvimento')
        ->assertDontSee('admin@teste.com');
});

test('can login instantly using loginAs method in local environment', function () {
    app()['env'] = 'local';

    // Create the test user
    $user = User::factory()->create([
        'email' => 'admin@teste.com',
    ]);

    Livewire::test(Login::class)
        ->call('loginAs', 'admin@teste.com')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('cannot login using loginAs method in non-local environment', function () {
    app()['env'] = 'production';

    // Create the test user
    User::factory()->create([
        'email' => 'admin@teste.com',
    ]);

    Livewire::test(Login::class)
        ->call('loginAs', 'admin@teste.com')
        ->assertStatus(403);

    $this->assertGuest();
});
