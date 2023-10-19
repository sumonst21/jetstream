<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\OrganizationMemberManager;
use Livewire\Livewire;

test('organization members can be removed from organizations', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('organizationMemberIdBeingRemoved', $otherUser->id)
        ->call('removeOrganizationMember');

    expect($user->currentOrganization->fresh()->users)->toHaveCount(0);
});

test('only organization owner can remove organization members', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('organizationMemberIdBeingRemoved', $user->id)
        ->call('removeOrganizationMember')
        ->assertStatus(403);
});
