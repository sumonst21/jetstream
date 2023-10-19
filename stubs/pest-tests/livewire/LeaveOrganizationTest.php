<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\OrganizationMemberManager;
use Livewire\Livewire;

test('users can leave organizations', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->call('leaveOrganization');

    expect($user->currentOrganization->fresh()->users)->toHaveCount(0);
});

test('organization owners cant leave their own organization', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->call('leaveOrganization')
        ->assertHasErrors(['organization']);

    expect($user->currentOrganization->fresh())->not->toBeNull();
});
