<script setup>
import { ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    organization: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const page = usePage();

const addOrganizationMemberForm = useForm({
    email: '',
    role: null,
});

const updateRoleForm = useForm({
    role: null,
});

const leaveOrganizationForm = useForm({});
const removeOrganizationMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingOrganization = ref(false);
const organizationMemberBeingRemoved = ref(null);

const addOrganizationMember = () => {
    addOrganizationMemberForm.post(route('organization-members.store', props.organization), {
        errorBag: 'addOrganizationMember',
        preserveScroll: true,
        onSuccess: () => addOrganizationMemberForm.reset(),
    });
};

const cancelOrganizationInvitation = (invitation) => {
    router.delete(route('organization-invitations.destroy', invitation), {
        preserveScroll: true,
    });
};

const manageRole = (organizationMember) => {
    managingRoleFor.value = organizationMember;
    updateRoleForm.role = organizationMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('organization-members.update', [props.organization, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => currentlyManagingRole.value = false,
    });
};

const confirmLeavingOrganization = () => {
    confirmingLeavingOrganization.value = true;
};

const leaveOrganization = () => {
    leaveOrganizationForm.delete(route('organization-members.destroy', [props.organization, page.props.auth.user]));
};

const confirmOrganizationMemberRemoval = (organizationMember) => {
    organizationMemberBeingRemoved.value = organizationMember;
};

const removeOrganizationMember = () => {
    removeOrganizationMemberForm.delete(route('organization-members.destroy', [props.organization, organizationMemberBeingRemoved.value]), {
        errorBag: 'removeOrganizationMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => organizationMemberBeingRemoved.value = null,
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find(r => r.key === role).name;
};
</script>

<template>
    <div>
        <div v-if="userPermissions.canAddOrganizationMembers">
            <SectionBorder />

            <!-- Add Organization Member -->
            <FormSection @submitted="addOrganizationMember">
                <template #title>
                    Add Organization Member
                </template>

                <template #description>
                    Add a new organization member to your organization, allowing them to collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                            Please provide the email address of the person you would like to add to this organization.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="addOrganizationMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="addOrganizationMemberForm.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                        <InputLabel for="roles" value="Role" />
                        <InputError :message="addOrganizationMemberForm.errors.role" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                :class="{'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                @click="addOrganizationMemberForm.role = role.key"
                            >
                                <div :class="{'opacity-50': addOrganizationMemberForm.role && addOrganizationMemberForm.role != role.key}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': addOrganizationMemberForm.role == role.key}">
                                            {{ role.name }}
                                        </div>

                                        <svg v-if="addOrganizationMemberForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-left">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="addOrganizationMemberForm.recentlySuccessful" class="mr-3">
                        Added.
                    </ActionMessage>

                    <PrimaryButton :class="{ 'opacity-25': addOrganizationMemberForm.processing }" :disabled="addOrganizationMemberForm.processing">
                        Add
                    </PrimaryButton>
                </template>
            </FormSection>
        </div>

        <div v-if="organization.organization_invitations.length > 0 && userPermissions.canAddOrganizationMembers">
            <SectionBorder />

            <!-- Organization Member Invitations -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Pending Organization Invitations
                </template>

                <template #description>
                    These people have been invited to your organization and have been sent an invitation email. They may join the organization by accepting the email invitation.
                </template>

                <!-- Pending Organization Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="invitation in organization.organization_invitations" :key="invitation.id" class="flex items-center justify-between">
                            <div class="text-gray-600 dark:text-gray-400">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Organization Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveOrganizationMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelOrganizationInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <div v-if="organization.users.length > 0">
            <SectionBorder />

            <!-- Manage Organization Members -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Organization Members
                </template>

                <template #description>
                    All of the people that are part of this organization.
                </template>

                <!-- Organization Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="user in organization.users" :key="user.id" class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name">
                                <div class="ml-4 dark:text-white">
                                    {{ user.name }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Organization Member Role -->
                                <button
                                    v-if="userPermissions.canUpdateOrganizationMembers && availableRoles.length"
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div v-else-if="availableRoles.length" class="ml-2 text-sm text-gray-400">
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Organization -->
                                <button
                                    v-if="$page.props.auth.user.id === user.id"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingOrganization"
                                >
                                    Leave
                                </button>

                                <!-- Remove Organization Member -->
                                <button
                                    v-else-if="userPermissions.canRemoveOrganizationMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmOrganizationMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <!-- Role Management Modal -->
        <DialogModal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            :class="{'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': updateRoleForm.role === role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg v-if="updateRoleForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="currentlyManagingRole = false">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Leave Organization Confirmation Modal -->
        <ConfirmationModal :show="confirmingLeavingOrganization" @close="confirmingLeavingOrganization = false">
            <template #title>
                Leave Organization
            </template>

            <template #content>
                Are you sure you would like to leave this organization?
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingLeavingOrganization = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': leaveOrganizationForm.processing }"
                    :disabled="leaveOrganizationForm.processing"
                    @click="leaveOrganization"
                >
                    Leave
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Remove Organization Member Confirmation Modal -->
        <ConfirmationModal :show="organizationMemberBeingRemoved" @close="organizationMemberBeingRemoved = null">
            <template #title>
                Remove Organization Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the organization?
            </template>

            <template #footer>
                <SecondaryButton @click="organizationMemberBeingRemoved = null">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': removeOrganizationMemberForm.processing }"
                    :disabled="removeOrganizationMemberForm.processing"
                    @click="removeOrganizationMember"
                >
                    Remove
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
