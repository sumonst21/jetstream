<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesOrganizations;
use Laravel\Jetstream\Events\AddingOrganization;
use Laravel\Jetstream\Jetstream;

class CreateOrganization implements CreatesOrganizations
{
    /**
     * Validate and create a new organization for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Organization
    {
        Gate::forUser($user)->authorize('create', Jetstream::newOrganizationModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createOrganization');

        AddingOrganization::dispatch($user);

        $user->switchOrganization($organization = $user->ownedOrganizations()->create([
            'name' => $input['name'],
            'personal_organization' => false,
        ]));

        return $organization;
    }
}
