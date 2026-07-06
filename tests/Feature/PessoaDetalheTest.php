<?php

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from client details', function () {
    $pessoa = Pessoa::factory()->create();
    $response = $this->get(route('pessoas.show', $pessoa));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view client details page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $pessoa = Pessoa::factory()->create();

    $response = $this->get(route('pessoas.show', $pessoa));
    $response->assertOk();
    $response->assertSee('Processos');
    $response->assertSee('Vínculos');
});
