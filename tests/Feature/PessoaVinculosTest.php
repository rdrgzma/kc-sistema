<?php

use App\Livewire\PessoaVinculosManager;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('can link a natural person (PF) to a legal entity (PJ)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $pf = Pessoa::factory()->create([
        'tipo' => 'PF',
        'nome_razao' => 'João da Silva',
        'cpf_cnpj' => '123.456.789-00',
    ]);

    $pj = Pessoa::factory()->create([
        'tipo' => 'PJ',
        'nome_razao' => 'Silva Advogados LTDA',
        'cpf_cnpj' => '12.345.678/0001-00',
    ]);

    Livewire::test(PessoaVinculosManager::class, ['pessoa' => $pf])
        ->callTableAction('vincular', data: [
            'pessoa_id' => $pj->id,
        ])
        ->assertHasNoTableActionErrors();

    // Verify it is in database
    $this->assertDatabaseHas('pessoa_vinculos', [
        'pessoa_fisica_id' => $pf->id,
        'pessoa_juridica_id' => $pj->id,
    ]);

    // Verify relationships
    expect($pf->pessoasJuridicas()->count())->toBe(1);
    expect($pj->pessoasFisicas()->count())->toBe(1);
    expect($pf->pessoasJuridicas->first()->id)->toBe($pj->id);
    expect($pj->pessoasFisicas->first()->id)->toBe($pf->id);
});

test('can remove link between natural person (PF) and legal entity (PJ)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $pf = Pessoa::factory()->create([
        'tipo' => 'PF',
        'nome_razao' => 'João da Silva',
        'cpf_cnpj' => '123.456.789-00',
    ]);

    $pj = Pessoa::factory()->create([
        'tipo' => 'PJ',
        'nome_razao' => 'Silva Advogados LTDA',
        'cpf_cnpj' => '12.345.678/0001-00',
    ]);

    // Create link
    $pf->pessoasJuridicas()->attach($pj->id);

    Livewire::test(PessoaVinculosManager::class, ['pessoa' => $pf])
        ->callTableAction('desvincular', $pj)
        ->assertHasNoTableActionErrors();

    // Verify deleted
    $this->assertDatabaseMissing('pessoa_vinculos', [
        'pessoa_fisica_id' => $pf->id,
        'pessoa_juridica_id' => $pj->id,
    ]);
});
