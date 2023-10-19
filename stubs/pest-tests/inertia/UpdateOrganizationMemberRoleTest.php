<?php

use App\Models\User;

test('organization member roles can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $response = $this->put('/organizations/'.$user->currentOrganization->id.'/members/'.$otherUser->id, [
        'role' => 'editor',
    ]);

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

    $response = $this->put('/organizations/'.$user->currentOrganization->id.'/members/'.$otherUser->id, [
        'role' => 'editor',
    ]);

    expect($otherUser->fresh()->hasOrganizationRole(
        $user->currentOrganization->fresh(), 'admin'
    ))->toBeTrue();
});
