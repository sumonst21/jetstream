<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Mail\OrganizationInvitation;

test('organization members can be invited to organization', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $response = $this->post('/organizations/'.$user->currentOrganization->id.'/members', [
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    Mail::assertSent(OrganizationInvitation::class);

    expect($user->currentOrganization->fresh()->organizationInvitations)->toHaveCount(1);
})->skip(function () {
    return ! Features::sendsOrganizationInvitations();
}, 'Organization invitations not enabled.');

test('organization member invitations can be cancelled', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $invitation = $user->currentOrganization->organizationInvitations()->create([
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    $response = $this->delete('/organization-invitations/'.$invitation->id);

    expect($user->currentOrganization->fresh()->organizationInvitations)->toHaveCount(0);
})->skip(function () {
    return ! Features::sendsOrganizationInvitations();
}, 'Organization invitations not enabled.');
