<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\AddsOrganizationMembers;
use Laravel\Jetstream\Events\AddingOrganizationMember;
use Laravel\Jetstream\Events\OrganizationMemberAdded;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddOrganizationMember implements AddsOrganizationMembers
{
    /**
     * Add a new organization member to the given organization.
     */
    public function add(User $user, Organization $organization, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addOrganizationMember', $organization);

        $this->validate($organization, $email, $role);

        $newOrganizationMember = Jetstream::findUserByEmailOrFail($email);

        AddingOrganizationMember::dispatch($organization, $newOrganizationMember);

        $organization->users()->attach(
            $newOrganizationMember, ['role' => $role]
        );

        OrganizationMemberAdded::dispatch($organization, $newOrganizationMember);
    }

    /**
     * Validate the add member operation.
     */
    protected function validate(Organization $organization, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnOrganization($organization, $email)
        )->validateWithBag('addOrganizationMember');
    }

    /**
     * Get the validation rules for adding a organization member.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
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
