<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteOrganizationForm from '@/Pages/Organizations/Partials/DeleteOrganizationForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import OrganizationMemberManager from '@/Pages/Organizations/Partials/OrganizationMemberManager.vue';
import UpdateOrganizationNameForm from '@/Pages/Organizations/Partials/UpdateOrganizationNameForm.vue';

defineProps({
    organization: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Organization Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Organization Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <UpdateOrganizationNameForm :organization="organization" :permissions="permissions" />

                <OrganizationMemberManager
                    class="mt-10 sm:mt-0"
                    :organization="organization"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template v-if="permissions.canDeleteOrganization && ! organization.personal_organization">
                    <SectionBorder />

                    <DeleteOrganizationForm class="mt-10 sm:mt-0" :organization="organization" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
