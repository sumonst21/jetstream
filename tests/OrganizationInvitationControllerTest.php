<?php

namespace Laravel\Jetstream\Tests;

use App\Actions\Jetstream\CreateOrganization;
use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\Contracts\AddsOrganizationMembers;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Tests\Fixtures\OrganizationPolicy;
use Laravel\Jetstream\Tests\Fixtures\User;

class OrganizationInvitationControllerTest extends OrchestraTestCase
{
    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('jetstream.stack', 'inertia');
        $app['config']->set('jetstream.features', ['organizations']);

        Gate::policy(Organization::class, OrganizationPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_organization_invitations_can_be_accepted()
    {
        $this->mock(AddsOrganizationMembers::class)->shouldReceive('add')->once();

        Jetstream::role('admin', 'Admin', ['foo', 'bar']);
        Jetstream::role('editor', 'Editor', ['baz', 'qux']);

        $organization = $this->createOrganization();

        $invitation = $organization->organizationInvitations()->create(['email' => 'adam@laravel.com', 'role' => 'admin']);

        $url = URL::signedRoute('organization-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($organization->owner)->get($url);

        $response->assertRedirect();
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
