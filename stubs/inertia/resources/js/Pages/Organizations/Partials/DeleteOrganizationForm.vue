<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    organization: Object,
});

const confirmingOrganizationDeletion = ref(false);
const form = useForm({});

const confirmOrganizationDeletion = () => {
    confirmingOrganizationDeletion.value = true;
};

const deleteOrganization = () => {
    form.delete(route('organizations.destroy', props.organization), {
        errorBag: 'deleteOrganization',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Delete Organization
        </template>

        <template #description>
            Permanently delete this organization.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Once a organization is deleted, all of its resources and data will be permanently deleted. Before deleting this organization, please download any data or information regarding this organization that you wish to retain.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmOrganizationDeletion">
                    Delete Organization
                </DangerButton>
            </div>

            <!-- Delete Organization Confirmation Modal -->
            <ConfirmationModal :show="confirmingOrganizationDeletion" @close="confirmingOrganizationDeletion = false">
                <template #title>
                    Delete Organization
                </template>

                <template #content>
                    Are you sure you want to delete this organization? Once a organization is deleted, all of its resources and data will be permanently deleted.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingOrganizationDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteOrganization"
                    >
                        Delete Organization
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
