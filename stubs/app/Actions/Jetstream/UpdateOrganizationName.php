<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\UpdatesOrganizationNames;

class UpdateOrganizationName implements UpdatesOrganizationNames
{
    /**
     * Validate and update the given organization's name.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, Organization $organization, array $input): void
    {
        Gate::forUser($user)->authorize('update', $organization);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateOrganizationName');

        $organization->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
