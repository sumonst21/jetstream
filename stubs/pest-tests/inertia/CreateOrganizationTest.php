<?php

use App\Models\User;

test('organizations can be created', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $response = $this->post('/organizations', [
        'name' => 'Test Organization',
    ]);

    expect($user->fresh()->ownedOrganizations)->toHaveCount(2);
    expect($user->fresh()->ownedOrganizations()->latest('id')->first()->name)->toEqual('Test Organization');
});
