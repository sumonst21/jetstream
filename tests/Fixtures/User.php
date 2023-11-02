<?php

namespace Laravel\Jetstream\Tests\Fixtures;

use App\Models\User as BaseUser;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasOrganizations;
use Laravel\Sanctum\HasApiTokens;

class User extends BaseUser
{
    use HasApiTokens, HasOrganizations, HasProfilePhoto;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
