<?php

use App\Livewire\Admin\AnalyticsManager;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from analytics', function () {
    $response = $this->get(route('admin.analytics'));
    $response->assertRedirect(route('login'));
});

test('users without Administrator or Partner roles cannot access analytics', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.analytics'));
    $response->assertForbidden();
});

test('users with Administrator role can access analytics', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $response = $this->get(route('admin.analytics'));
    $response->assertOk();
});

test('activities are listed on the table and show subject links', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $pessoa = Pessoa::factory()->create([
        'nome_razao' => 'Empresa Teste Limitada',
        'tipo' => 'PJ',
    ]);

    // Create activity
    $activity = activity()
        ->performedOn($pessoa)
        ->causedBy($user)
        ->withProperties(['attributes' => ['nome_razao' => 'Empresa Teste Limitada'], 'old' => ['nome_razao' => 'Empresa Antiga']])
        ->log('Cliente cadastrado');

    Livewire::test(AnalyticsManager::class)
        ->assertCanSeeTableRecords([$activity])
        ->assertTableColumnExists('created_at')
        ->assertTableColumnExists('causer.name')
        ->assertTableColumnExists('description')
        ->assertTableColumnExists('subject');
});

test('can export analytics logs as CSV', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $pessoa = Pessoa::factory()->create([
        'nome_razao' => 'Empresa Teste Limitada',
        'tipo' => 'PJ',
    ]);

    activity()
        ->performedOn($pessoa)
        ->causedBy($user)
        ->log('Cliente cadastrado');

    $response = $this->get(route('admin.analytics.export.csv'));
    $response->assertOk();
    $response->assertHeader('Content-Disposition', 'attachment; filename=relatorio-analytics-'.now()->format('d-m-Y').'.csv');
});

test('can export analytics logs as PDF', function () {
    $role = Role::create(['name' => 'Administrador']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $pessoa = Pessoa::factory()->create([
        'nome_razao' => 'Empresa Teste Limitada',
        'tipo' => 'PJ',
    ]);

    activity()
        ->performedOn($pessoa)
        ->causedBy($user)
        ->log('Cliente cadastrado');

    $response = $this->get(route('admin.analytics.export.pdf'));
    $response->assertOk();
    $response->assertHeader('Content-Disposition', 'attachment; filename=relatorio-analytics-'.now()->format('d-m-Y').'.pdf');
});
