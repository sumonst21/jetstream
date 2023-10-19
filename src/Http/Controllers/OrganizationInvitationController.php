<?php

namespace Laravel\Jetstream\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Contracts\AddsOrganizationMembers;
use Laravel\Jetstream\Jetstream;

class OrganizationInvitationController extends Controller
{
    /**
     * Accept a organization invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invitationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, $invitationId)
    {
        $model = Jetstream::organizationInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        app(AddsOrganizationMembers::class)->add(
            $invitation->organization->owner,
            $invitation->organization,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :organization organization.', ['organization' => $invitation->organization->name]),
        );
    }

    /**
     * Cancel the given organization invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invitationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $invitationId)
    {
        $model = Jetstream::organizationInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removeOrganizationMember', $invitation->organization)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
