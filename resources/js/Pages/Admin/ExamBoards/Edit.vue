<!-- resources/js/Pages/Admin/ExamBoards/Edit.vue -->
<template>
    <AdminLayout>
        <Head :title="`Edit ${exam_board.name}`" />

        <div class="max-w-3xl mx-auto py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-2">
                    <Link
                        :href="route('admin.exam-boards.index')"
                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                    >
                        ← Back to Exam Boards
                    </Link>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Edit {{ exam_board.name }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Update exam board information and subjects
                </p>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form @submit.prevent="submit">
                    <div class="space-y-6 p-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Exam Board Name *
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="form.name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.name }"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Code -->
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">
                                        Code *
                                    </label>
                                    <input
                                        type="text"
                                        id="code"
                                        v-model="form.code"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.code }"
                                    />
                                    <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.code }}
                                    </p>
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">
                                        Country *
                                    </label>
                                    <select
                                        id="country"
                                        v-model="form.country"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.country }"
                                    >
                                        <option value="">Select a country</option>
                                        <option
                                            v-for="(name, value) in countries"
                                            :key="value"
                                            :value="value"
                                        >
                                            {{ name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.country }}
                                    </p>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="flex items-center mt-6">
                                        <input
                                            type="checkbox"
                                            v-model="form.is_active"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Active</span>
                                    </label>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Active exam boards can be selected by students
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': form.errors.description }"
                                />
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Subjects -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Subjects</h3>
                            <div class="space-y-4">
                                <div v-for="(subject, index) in form.subjects" :key="index" class="flex items-center space-x-2">
                                    <input
                                        type="text"
                                        v-model="form.subjects[index]"
                                        placeholder="Enter subject name"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors[`subjects.${index}`] }"
                                    />
                                    <button
                                        type="button"
                                        @click="removeSubject(index)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                                <p v-if="form.errors.subjects" class="text-sm text-red-600">
                                    {{ form.errors.subjects }}
                                </p>
                                <button
                                    type="button"
                                    @click="addSubject"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <PlusIcon class="h-4 w-4 mr-2" />
                                    Add Subject
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            :disabled="exam_board.courses_count > 0 || exam_board.student_profiles_count > 0"
                        >
                            Delete
                        </button>
                        <div class="flex space-x-3">
                            <Link
                                :href="route('admin.exam-boards.index')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Exam Board</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    exam_board: Object,
    countries: Object,
})

const form = useForm({
    name: props.exam_board.name,
    code: props.exam_board.code,
    description: props.exam_board.description,
    country: props.exam_board.country,
    subjects: props.exam_board.subjects?.length ? [...props.exam_board.subjects] : [''],
    is_active: props.exam_board.is_active,
})

const addSubject = () => {
    form.subjects.push('')
}

const removeSubject = (index) => {
    if (form.subjects.length > 1) {
        form.subjects.splice(index, 1)
    }
}

const submit = () => {
    form.put(route('admin.exam-boards.update', props.exam_board.id))
}

const confirmDelete = () => {
    if (props.exam_board.courses_count > 0 || props.exam_board.student_profiles_count > 0) {
        alert('Cannot delete exam board that is in use by courses or students.')
        return
    }

    if (confirm(`Are you sure you want to delete "${props.exam_board.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.exam-boards.destroy', props.exam_board.id))
    }
}
</script>
