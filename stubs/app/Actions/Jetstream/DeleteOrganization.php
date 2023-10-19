<?php

namespace App\Actions\Jetstream;

use App\Models\Organization;
use Laravel\Jetstream\Contracts\DeletesOrganizations;

class DeleteOrganization implements DeletesOrganizations
{
    /**
     * Delete the given organization.
     */
    public function delete(Organization $organization): void
    {
        $organization->purge();
    }
}
