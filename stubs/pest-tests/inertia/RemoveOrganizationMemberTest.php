<?php

use App\Models\User;

test('organization members can be removed from organizations', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $response = $this->delete('/organizations/'.$user->currentOrganization->id.'/members/'.$otherUser->id);

    expect($user->currentOrganization->fresh()->users)->toHaveCount(0);
});

test('only organization owner can remove organization members', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $response = $this->delete('/organizations/'.$user->currentOrganization->id.'/members/'.$user->id);

    $response->assertStatus(403);
});
