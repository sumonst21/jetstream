<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\OrganizationMemberManager;
use Laravel\Jetstream\Mail\OrganizationInvitation;
use Livewire\Livewire;

test('organization members can be invited to organization', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('addOrganizationMemberForm', [
            'email' => 'test@example.com',
            'role' => 'admin',
        ])->call('addOrganizationMember');

    Mail::assertSent(OrganizationInvitation::class);

    expect($user->currentOrganization->fresh()->organizationInvitations)->toHaveCount(1);
})->skip(function () {
    return ! Features::sendsOrganizationInvitations();
}, 'Organization invitations not enabled.');

test('organization member invitations can be cancelled', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalOrganization()->create());

    // Add the organization member...
    $component = Livewire::test(OrganizationMemberManager::class, ['organization' => $user->currentOrganization])
        ->set('addOrganizationMemberForm', [
            'email' => 'test@example.com',
            'role' => 'admin',
        ])->call('addOrganizationMember');

    $invitationId = $user->currentOrganization->fresh()->organizationInvitations->first()->id;

    // Cancel the organization invitation...
    $component->call('cancelOrganizationInvitation', $invitationId);

    expect($user->currentOrganization->fresh()->organizationInvitations)->toHaveCount(0);
})->skip(function () {
    return ! Features::sendsOrganizationInvitations();
}, 'Organization invitations not enabled.');
