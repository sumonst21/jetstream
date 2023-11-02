<?php

namespace Laravel\Jetstream\Tests;

use App\Actions\Jetstream\CreateOrganization;
use App\Actions\Jetstream\UpdateOrganizationName;
use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Tests\Fixtures\OrganizationPolicy;
use Laravel\Jetstream\Tests\Fixtures\User;

class UpdateOrganizationTest extends OrchestraTestCase
{
    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        Gate::policy(Organization::class, OrganizationPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_organization_name_can_be_updated()
    {
        $organization = $this->createOrganization();

        $action = new UpdateOrganizationName;

        $action->update($organization->owner, $organization, ['name' => 'Test Organization Updated']);

        $this->assertSame('Test Organization Updated', $organization->fresh()->name);
    }

    public function test_name_is_required()
    {
        $this->expectException(ValidationException::class);

        $organization = $this->createOrganization();

        $action = new UpdateOrganizationName;

        $action->update($organization->owner, $organization, ['name' => '']);
    }

    protected function createOrganization()
    {
        $action = new CreateOrganization;

        $user = User::forceCreate([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
            'password' => 'secret',
        ]);

        return $action->create($user, ['name' => 'Test Organization']);
    }
}
