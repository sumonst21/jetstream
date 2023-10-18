<?php

namespace Laravel\Jetstream\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Contracts\UpdatesOrganizationNames;
use Livewire\Component;

class UpdateOrganizationNameForm extends Component
{
    /**
     * The organization instance.
     *
     * @var mixed
     */
    public $organization;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * Mount the component.
     *
     * @param  mixed  $organization
     * @return void
     */
    public function mount($organization)
    {
        $this->organization = $organization;

        $this->state = $organization->withoutRelations()->toArray();
    }

    /**
     * Update the organization's name.
     *
     * @param  \Laravel\Jetstream\Contracts\UpdatesOrganizationNames  $updater
     * @return void
     */
    public function updateOrganizationName(UpdatesOrganizationNames $updater)
    {
        $this->resetErrorBag();

        $updater->update($this->user, $this->organization, $this->state);

        $this->dispatch('saved');

        $this->dispatch('refresh-navigation-menu');
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('organizations.update-organization-name-form');
    }
}
