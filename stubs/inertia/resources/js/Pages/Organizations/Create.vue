<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateOrganizationForm from '@/Pages/Organizations/Partials/CreateOrganizationForm.vue';
</script>

<template>
    <AppLayout title="Create Organization">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Organization
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateOrganizationForm />
            </div>
        </div>
    </AppLayout>
</template>
