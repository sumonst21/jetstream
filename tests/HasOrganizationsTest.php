<?php

namespace Laravel\Jetstream\Tests;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\OwnerRole;
use Laravel\Jetstream\Role;
use Laravel\Jetstream\Tests\Fixtures\User as UserFixture;

class HasOrganizationsTest extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        Jetstream::$permissions = [];
        Jetstream::$roles = [];

        Jetstream::useUserModel(UserFixture::class);
    }

    public function test_organizationRole_returns_an_OwnerRole_for_the_organization_owner(): void
    {
        $organization = Organization::factory()->create();

        $this->assertInstanceOf(OwnerRole::class, $organization->owner->organizationRole($organization));
    }

    public function test_organizationRole_returns_the_matching_role(): void
    {
        Jetstream::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Admin Description');

        $organization = Organization::factory()
            ->hasAttached(User::factory(), [
                'role' => 'admin',
            ])
            ->create();
        $role = $organization->users->first()->organizationRole($organization);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertSame('admin', $role->key);
    }

    public function test_organizationRole_returns_null_if_the_user_does_not_belong_to_the_organization(): void
    {
        $organization = Organization::factory()->create();

        $this->assertNull((new UserFixture())->organizationRole($organization));
    }

    public function test_organizationRole_returns_null_if_the_user_does_not_have_a_role_on_the_site(): void
    {
        $organization = Organization::factory()
            ->has(User::factory())
            ->create();

        $this->assertNull($organization->users->first()->organizationRole($organization));
    }

    public function test_organizationPermissions_returns_all_for_organization_owners(): void
    {
        $organization = Organization::factory()->create();

        $this->assertSame(['*'], $organization->owner->organizationPermissions($organization));
    }

    public function test_organizationPermissions_returns_empty_for_non_members(): void
    {
        $organization = Organization::factory()->create();

        $this->assertSame([], (new UserFixture())->organizationPermissions($organization));
    }

    public function test_organizationPermissions_returns_permissions_for_the_users_role(): void
    {
        Jetstream::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Admin Description');

        $organization = Organization::factory()
            ->hasAttached(User::factory(), [
                'role' => 'admin',
            ])
            ->create();

        $this->assertSame(['read', 'create'], $organization->users->first()->organizationPermissions($organization));
    }

    public function test_organizationPermissions_returns_empty_permissions_for_members_without_a_defined_role(): void
    {
        Jetstream::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Admin Description');

        $organization = Organization::factory()
            ->has(User::factory())
            ->create();

        $this->assertSame([], $organization->users->first()->organizationPermissions($organization));
    }
}
