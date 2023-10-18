<?php

use App\Models\User;

test('organization names can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $response = $this->put('/organizations/'.$user->currentOrganization->id, [
        'name' => 'Test Organization',
    ]);

    expect($user->fresh()->ownedOrganizations)->toHaveCount(1);
    expect($user->currentOrganization->fresh()->name)->toEqual('Test Organization');
});
