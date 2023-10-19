<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use App\Models\User;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\InvitesOrganizationMembers;
use Laravel\Jetstream\Events\InvitingOrganizationMember;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Mail\OrganizationInvitation;
use Laravel\Jetstream\Rules\Role;

class InviteOrganizationMember implements InvitesOrganizationMembers
{
    /**
     * Invite a new organization member to the given organization.
     */
    public function invite(User $user, Organization $organization, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addOrganizationMember', $organization);

        $this->validate($organization, $email, $role);

        InvitingOrganizationMember::dispatch($organization, $email, $role);

        $invitation = $organization->organizationInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new OrganizationInvitation($invitation));
    }

    /**
     * Validate the invite member operation.
     */
    protected function validate(Organization $organization, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($organization), [
            'email.unique' => __('This user has already been invited to the organization.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnOrganization($organization, $email)
        )->validateWithBag('addOrganizationMember');
    }

    /**
     * Get the validation rules for inviting a organization member.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Organization $organization): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('organization_invitations')->where(function (Builder $query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                }),
            ],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the organization.
     */
    protected function ensureUserIsNotAlreadyOnOrganization(Organization $organization, string $email): Closure
    {
        return function ($validator) use ($organization, $email) {
            $validator->errors()->addIf(
                $organization->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the organization.')
            );
        };
    }
}
