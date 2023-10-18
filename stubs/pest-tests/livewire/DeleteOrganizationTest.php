<?php

use App\Models\Organization;
use App\Models\User;
use Laravel\Jetstream\Http\Livewire\DeleteOrganizationForm;
use Livewire\Livewire;

test('organizations can be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->ownedOrganizations()->save($organization = Organization::factory()->make([
        'personal_organization' => false,
    ]));

    $organization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'test-role']
    );

    $component = Livewire::test(DeleteOrganizationForm::class, ['organization' => $organization->fresh()])
        ->call('deleteOrganization');

    expect($organization->fresh())->toBeNull();
    expect($otherUser->fresh()->organizations)->toHaveCount(0);
});

test('personal organizations cant be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $component = Livewire::test(DeleteOrganizationForm::class, ['organization' => $user->currentOrganization])
        ->call('deleteOrganization')
        ->assertHasErrors(['organization']);

    expect($user->currentOrganization->fresh())->not->toBeNull();
});
