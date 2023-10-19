<?php

use App\Models\User;

test('users can leave organizations', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $user->currentOrganization->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $response = $this->delete('/organizations/'.$user->currentOrganization->id.'/members/'.$otherUser->id);

    expect($user->currentOrganization->fresh()->users)->toHaveCount(0);
});

test('organization owners cant leave their own organization', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $response = $this->delete('/organizations/'.$user->currentOrganization->id.'/members/'.$user->id);

    $response->assertSessionHasErrorsIn('removeOrganizationMember', ['organization']);

    expect($user->currentOrganization->fresh())->not->toBeNull();
});
