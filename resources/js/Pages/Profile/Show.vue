<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

// Compute which layout to use - FIXED: use page.props instead of $page.props
const userRole = computed(() => page.props.auth?.user?.roles?.[0] || 'student');
const Layout = computed(() => userRole.value === 'admin' ? AdminLayout : StudentLayout);

// Computed properties for page props
const jetstream = computed(() => page.props.jetstream || {});
const authUser = computed(() => page.props.auth?.user);
</script>

<template>
    <component :is="Layout" title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="authUser" />
                    <SectionBorder />
                </div>

                <div v-if="jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                    <SectionBorder />
                </div>

                <div v-if="jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />
                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <template v-if="jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />
                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>
    </component>
</template>
