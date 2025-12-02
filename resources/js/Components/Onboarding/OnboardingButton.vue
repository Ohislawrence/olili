<template>
    <div class="relative">
        <button
            @click="restartOnboarding"
            class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors"
        >
            <QuestionMarkCircleIcon class="h-5 w-5" />
            <span>Show Tour</span>
        </button>
        
        <!-- Restart Confirmation Modal -->
        <TransitionRoot appear :show="showConfirmation" as="template">
            <Dialog as="div" class="relative z-50" @close="showConfirmation = false">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/30" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl"
                            >
                                <DialogTitle as="h3" class="text-lg font-medium text-gray-900 mb-4">
                                    Restart Onboarding Tour
                                </DialogTitle>
                                
                                <p class="text-gray-600 mb-6">
                                    This will restart the feature tour. Are you sure?
                                </p>
                                
                                <div class="flex justify-end space-x-3">
                                    <button
                                        @click="showConfirmation = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="confirmRestart"
                                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-md hover:shadow-lg transition-all duration-200"
                                        
                                    >
                                        Restart Tour
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue';
import { QuestionMarkCircleIcon } from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';

const showConfirmation = ref(false);

const restartOnboarding = () => {
    showConfirmation.value = true;
};

const confirmRestart = () => {
    router.post(route('onboarding.restart'), {}, {
        onSuccess: () => {
            showConfirmation.value = false;
            // Store flag to show onboarding on next page load
            localStorage.setItem('showOnboarding', 'true');
            // Reload page to show onboarding
            window.location.reload();
        }
    });
};
</script>