<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\OrganizationMemberManager;
use Livewire\Livewire;

test('organization member roles can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('managingRoleFor', $otherUser)
        ->set('currentRole', 'editor')
        ->call('updateRole');

    expect($otherUser->fresh()->hasOrganizationRole(
        $user->currentOrganization->fresh(), 'editor'
    ))->toBeTrue();
});

test('only organization owner can update organization member roles', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('managingRoleFor', $otherUser)
        ->set('currentRole', 'editor')
        ->call('updateRole')
        ->assertStatus(403);

    expect($otherUser->fresh()->hasOrganizationRole(
        $user->currentOrganization->fresh(), 'admin'
    ))->toBeTrue();
});
