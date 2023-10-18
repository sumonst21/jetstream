<?php

use App\Models\Organization;
use App\Models\User;

test('organizations can be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $user->ownedOrganizations()->save($organization = Organization::factory()->make([
        'personal_organization' => false,
    ]));

    $organization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'test-role']
    );

    $response = $this->delete('/organizations/'.$organization->id);

    expect($organization->fresh())->toBeNull();
    expect($otherUser->fresh()->organizations)->toHaveCount(0);
});

test('personal organizations cant be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $response = $this->delete('/organizations/'.$user->currentOrganization->id);

    expect($user->currentOrganization->fresh())->not->toBeNull();
});
