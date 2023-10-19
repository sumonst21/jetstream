<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\OrganizationInvitation as JetstreamOrganizationInvitation;

class OrganizationInvitation extends JetstreamOrganizationInvitation
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the organization that the invitation belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Jetstream::organizationModel());
    }
}
