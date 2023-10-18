<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesOrganizations;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * The organization deleter implementation.
     *
     * @var \Laravel\Jetstream\Contracts\DeletesOrganizations
     */
    protected $deletesOrganizations;

    /**
     * Create a new action instance.
     */
    public function __construct(DeletesOrganizations $deletesOrganizations)
    {
        $this->deletesOrganizations = $deletesOrganizations;
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteOrganizations($user);
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
        });
    }

    /**
     * Delete the organizations and organization associations attached to the user.
     */
    protected function deleteOrganizations(User $user): void
    {
        $user->organizations()->detach();

        $user->ownedOrganizations->each(function (Organization $organization) {
            $this->deletesOrganizations->delete($organization);
        });
    }
}
