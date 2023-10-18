<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\UpdateOrganizationNameForm;
use Livewire\Livewire;

test('organization names can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    Livewire::test(UpdateOrganizationNameForm::class, ['organization' => $user->currentOrganization])
        ->set(['state' => ['name' => 'Test Organization']])
        ->call('updateOrganizationName');

    expect($user->fresh()->ownedOrganizations)->toHaveCount(1);
    expect($user->currentOrganization->fresh()->name)->toEqual('Test Organization');
});
