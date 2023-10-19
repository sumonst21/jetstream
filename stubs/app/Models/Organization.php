<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\OrganizationCreated;
use Laravel\Jetstream\Events\OrganizationDeleted;
use Laravel\Jetstream\Events\OrganizationUpdated;
use Laravel\Jetstream\Organization as JetstreamOrganization;

class Organization extends JetstreamOrganization
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_organization' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_organization',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => OrganizationCreated::class,
        'updated' => OrganizationUpdated::class,
        'deleted' => OrganizationDeleted::class,
    ];
}
