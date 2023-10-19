<x-form-section submit="updateOrganizationName">
    <x-slot name="title">
        {{ __('Organization Name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('The organization\'s name and owner information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Organization Owner Information -->
        <div class="col-span-6">
            <x-label value="{{ __('Organization Owner') }}" />

            <div class="flex items-center mt-2">
                <img class="w-12 h-12 rounded-full object-cover" src="{{ $organization->owner->profile_photo_url }}" alt="{{ $organization->owner->name }}">

                <div class="ml-4 leading-tight">
                    <div class="text-gray-900 dark:text-white">{{ $organization->owner->name }}</div>
                    <div class="text-gray-700 dark:text-gray-300 text-sm">{{ $organization->owner->email }}</div>
                </div>
            </div>
        </div>

        <!-- Organization Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Organization Name') }}" />

            <x-input id="name"
                        type="text"
                        class="mt-1 block w-full"
                        wire:model="state.name"
                        :disabled="! Gate::check('update', $organization)" />

            <x-input-error for="name" class="mt-2" />
        </div>
    </x-slot>

    @if (Gate::check('update', $organization))
        <x-slot name="actions">
            <x-action-message class="mr-3" on="saved">
                {{ __('Saved.') }}
            </x-action-message>

            <x-button>
                {{ __('Save') }}
            </x-button>
        </x-slot>
    @endif
</x-form-section>
