<script setup>
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    organization: Object,
    permissions: Object,
});

const form = useForm({
    name: props.organization.name,
});

const updateOrganizationName = () => {
    form.put(route('organizations.update', props.organization), {
        errorBag: 'updateOrganizationName',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateOrganizationName">
        <template #title>
            Organization Name
        </template>

        <template #description>
            The organization's name and owner information.
        </template>

        <template #form>
            <!-- Organization Owner Information -->
            <div class="col-span-6">
                <InputLabel value="Organization Owner" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="organization.owner.profile_photo_url" :alt="organization.owner.name">

                    <div class="ml-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">{{ organization.owner.name }}</div>
                        <div class="text-gray-700 dark:text-gray-300 text-sm">
                            {{ organization.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Organization Name" />

                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    :disabled="! permissions.canUpdateOrganization"
                />

                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template v-if="permissions.canUpdateOrganization" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
