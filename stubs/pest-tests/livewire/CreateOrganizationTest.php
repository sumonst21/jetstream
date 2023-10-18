<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\CreateOrganizationForm;
use Livewire\Livewire;

test('organizations can be created', function () {
    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    Livewire::test(CreateOrganizationForm::class)
        ->set(['state' => ['name' => 'Test Organization']])
        ->call('createOrganization');

    expect($user->fresh()->ownedOrganizations)->toHaveCount(2);
    expect($user->fresh()->ownedOrganizations()->latest('id')->first()->name)->toEqual('Test Organization');
});
