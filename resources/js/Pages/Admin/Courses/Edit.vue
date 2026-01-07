<!-- resources/js/Pages/Admin/Courses/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit: ${course.title}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
              <p class="mt-1 text-sm text-gray-600">
                Update course details and settings
              </p>
            </div>
            <Link
              :href="route('admin.courses.show', course.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Course
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-8">
              <!-- Course Basics Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Basics</h2>
                  <p class="mt-1 text-sm text-gray-500">Basic information about your course</p>
                </div>

                <!-- Course Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Title *
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    :class="{ 'border-red-300': form.errors.title }"
                  />
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                    {{ form.errors.title }}
                  </p>
                </div>

                <!-- Subject -->
                <div>
                  <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject *
                  </label>
                  <div class="mt-1">
                    <input
                      id="subject"
                      v-model="form.subject"
                      type="text"
                      required
                      list="subjects"
                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      :class="{ 'border-red-300': form.errors.subject }"
                    />
                    <datalist id="subjects">
                      <option v-for="subject in subjects" :key="subject" :value="subject" />
                    </datalist>
                  </div>
                  <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                    {{ form.errors.subject }}
                  </p>
                </div>

                <!-- Exam Board -->
                <div>
                  <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Board (Optional)
                  </label>
                  <select
                    id="exam_board_id"
                    v-model="form.exam_board_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option :value="null">No specific exam board</option>
                    <option
                      v-for="examBoard in exam_boards"
                      :key="examBoard.id"
                      :value="examBoard.id"
                    >
                      {{ examBoard.name }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Selecting an exam board will tailor content to specific syllabus requirements
                  </p>
                </div>
              </div>

              <!-- Course Details Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Details</h2>
                  <p class="mt-1 text-sm text-gray-500">Configure course settings and requirements</p>
                </div>

                <!-- Level -->
                <div>
                  <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Level *
                  </label>
                  <select
                    id="level"
                    v-model="form.level"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    :class="{ 'border-red-300': form.errors.level }"
                  >
                    <option value="">Select course level</option>
                    <option
                      v-for="(label, value) in levels"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">
                    {{ form.errors.level }}
                  </p>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Status *
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    :class="{ 'border-red-300': form.errors.status }"
                  >
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                    <option value="paused">Paused</option>
                    <option value="archived">Archived</option>
                    <option value="completed">Completed</option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                    {{ form.errors.status }}
                  </p>
                </div>

                <!-- Visibility -->
                <div>
                  <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                    Visibility *
                  </label>
                  <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label
                      v-for="option in visibilityOptions"
                      :key="option.value"
                      :class="[
                        'relative flex cursor-pointer rounded-lg border p-4 focus:outline-none',
                        form.visibility === option.value
                          ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500'
                          : 'border-gray-300'
                      ]"
                    >
                      <input
                        type="radio"
                        :value="option.value"
                        v-model="form.visibility"
                        class="sr-only"
                      />
                      <div class="flex flex-1">
                        <div class="flex flex-col">
                          <span class="flex items-center text-sm font-medium text-gray-900">
                            {{ option.label }}
                            <svg v-if="form.visibility === option.value" xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                          </span>
                          <span class="mt-1 text-xs text-gray-500">
                            {{ option.description }}
                          </span>
                        </div>
                      </div>
                    </label>
                  </div>
                  <p v-if="form.errors.visibility" class="mt-1 text-sm text-red-600">
                    {{ form.errors.visibility }}
                  </p>
                </div>

                <!-- Enrollment Limit -->
                <div>
                  <label for="enrollment_limit" class="block text-sm font-medium text-gray-700 mb-1">
                    Enrollment Limit (Optional)
                  </label>
                  <input
                    id="enrollment_limit"
                    v-model="form.enrollment_limit"
                    type="number"
                    min="0"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="Leave empty for unlimited"
                  />
                  <div class="mt-1 flex items-center text-sm text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.205a9 9 0 01-13.5 2.201" />
                    </svg>
                    <span>Currently enrolled: {{ course.current_enrollment || 0 }} students</span>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    Maximum number of students who can enroll. Leave empty for unlimited enrollment.
                  </p>
                </div>

                <!-- Estimated Duration -->
                <div>
                  <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                    Estimated Duration (Hours)
                  </label>
                  <input
                    id="estimated_duration_hours"
                    v-model="form.estimated_duration_hours"
                    type="number"
                    min="1"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    :class="{ 'border-red-300': form.errors.estimated_duration_hours }"
                  />
                  <p v-if="form.errors.estimated_duration_hours" class="mt-1 text-sm text-red-600">
                    {{ form.errors.estimated_duration_hours }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Approximate total study time for students to complete the course
                  </p>
                </div>

                <!-- Target Completion Date -->
                <div>
                  <label for="target_completion_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Suggested Completion Timeline
                  </label>
                  <input
                    id="target_completion_date"
                    v-model="form.target_completion_date"
                    type="date"
                    :min="minDate"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Suggested date for students to aim for completion (optional)
                  </p>
                </div>
              </div>

              <!-- Course Content Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Content</h2>
                  <p class="mt-1 text-sm text-gray-500">Define what students will learn</p>
                </div>

                <!-- Thumbnail Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Course Thumbnail
                  </label>
                  <div class="mt-1 flex items-center">
                    <div class="flex-shrink-0 h-32 w-48 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                      <div v-if="thumbnailPreview || course.thumbnail_url" class="h-full w-full">
                        <img :src="thumbnailPreview || course.thumbnail_url" alt="Thumbnail preview" class="h-full w-full object-cover" />
                      </div>
                      <div v-else class="text-center p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-xs text-gray-500">No thumbnail</p>
                        <p class="text-xs text-gray-400">PNG, JPG up to 2MB</p>
                      </div>
                    </div>
                    <div class="ml-4">
                      <input
                        id="thumbnail"
                        ref="thumbnailInput"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        @change="handleThumbnailChange"
                      />
                      <button
                        type="button"
                        @click="$refs.thumbnailInput.click()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ (thumbnailPreview || course.thumbnail_url) ? 'Change' : 'Upload' }} Thumbnail
                      </button>
                      <button
                        v-if="thumbnailPreview || course.thumbnail_url"
                        type="button"
                        @click="removeThumbnail"
                        class="ml-2 inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove
                      </button>
                    </div>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    Recommended size: 800x450 pixels. This will be displayed in the course catalog.
                  </p>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Description *
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    :class="{ 'border-red-300': form.errors.description }"
                  />
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                    {{ form.errors.description }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    A clear description helps students understand what to expect from the course.
                  </p>
                </div>

                <!-- Learning Objectives -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Learning Objectives
                  </label>
                  <div class="space-y-3">
                    <div
                      v-for="(objective, index) in form.learning_objectives"
                      :key="index"
                      class="flex gap-3"
                    >
                      <div class="flex-1">
                        <input
                          v-model="form.learning_objectives[index]"
                          type="text"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                          :placeholder="`Learning objective ${index + 1}`"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removeObjective(index)"
                        class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                        :disabled="form.learning_objectives.length <= 1"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addObjective"
                    class="mt-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Learning Objective
                  </button>
                  <p class="mt-1 text-sm text-gray-500">
                    What specific skills or knowledge will students gain from this course?
                  </p>
                </div>

                <!-- Prerequisites -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Prerequisites (Optional)
                  </label>
                  <div class="space-y-3">
                    <div
                      v-for="(prerequisite, index) in form.prerequisites"
                      :key="index"
                      class="flex gap-3"
                    >
                      <div class="flex-1">
                        <input
                          v-model="form.prerequisites[index]"
                          type="text"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                          :placeholder="`Prerequisite ${index + 1}`"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removePrerequisite(index)"
                        class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                        :disabled="form.prerequisites.length <= 1"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addPrerequisite"
                    class="mt-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Prerequisite
                  </button>
                  <p class="mt-1 text-sm text-gray-500">
                    What knowledge or skills should students have before taking this course?
                  </p>
                </div>
              </div>

              <!-- Danger Zone -->
              <div class="space-y-6 border-t border-gray-200 pt-8">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Danger Zone</h2>
                  <p class="mt-1 text-sm text-gray-500">Critical actions that cannot be undone</p>
                </div>

                <!-- Regenerate Course Content -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <div class="flex-1">
                      <h3 class="text-sm font-medium text-yellow-800">
                        Regenerate Course Content with AI
                      </h3>
                      <div class="mt-2 text-sm text-yellow-700">
                        <p>
                          This will regenerate all modules and topics using AI based on the updated course information.
                          <span class="font-semibold">Warning: This will delete all existing content and cannot be undone!</span>
                        </p>
                      </div>
                      <div class="mt-3">
                        <button
                          type="button"
                          @click="showRegenerateModal = true"
                          class="inline-flex items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                          Regenerate Course Structure
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.courses.show', course.id)"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                >
                  Cancel
                </Link>
                <button
                  type="button"
                  @click="resetForm"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                >
                  Reset Changes
                </button>
              </div>
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="saveDraft"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 disabled:opacity-50"
                >
                  Save as Draft
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                >
                  <template v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </template>
                  <template v-else>
                    Save Changes
                  </template>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Regenerate Confirmation Modal -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showRegenerateModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRegenerateModal = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.798-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Regenerate Course Content
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to regenerate the entire course structure? This action will:
                  </p>
                  <ul class="mt-2 text-sm text-red-600 space-y-1 list-disc list-inside">
                    <li>Delete all existing modules and topics</li>
                    <li>Remove all student progress data for this course</li>
                    <li>Generate new content based on current course settings</li>
                    <li>Cannot be undone</li>
                  </ul>
                  <div v-if="course.current_enrollment > 0" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm font-medium text-red-800">
                      ⚠️ Warning: {{ course.current_enrollment }} students are currently enrolled in this course!
                    </p>
                    <p class="mt-1 text-sm text-red-600">
                      Regenerating content will reset all student progress and may cause confusion.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="regenerateContent"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Regenerate Content
              </button>
              <button
                type="button"
                @click="showRegenerateModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  course: Object,
  exam_boards: Array,
  subjects: Array,
  levels: Object,
})

// State
const showRegenerateModal = ref(false)
const thumbnailPreview = ref(null)
const thumbnailInput = ref(null)
const removeThumbnailFlag = ref(false)

// Form
const form = reactive({
  title: props.course.title,
  subject: props.course.subject,
  description: props.course.description || '',
  exam_board_id: props.course.exam_board_id,
  level: props.course.level,
  status: props.course.status,
  visibility: props.course.visibility,
  enrollment_limit: props.course.enrollment_limit,
  estimated_duration_hours: props.course.estimated_duration_hours || 40,
  target_completion_date: props.course.target_completion_date
    ? new Date(props.course.target_completion_date).toISOString().split('T')[0]
    : '',
  learning_objectives: Array.isArray(props.course.learning_objectives)
    ? [...props.course.learning_objectives]
    : (props.course.learning_objectives ? [props.course.learning_objectives] : ['']),
  prerequisites: Array.isArray(props.course.prerequisites)
    ? [...props.course.prerequisites]
    : (props.course.prerequisites ? [props.course.prerequisites] : ['']),
  thumbnail: null,
  errors: {},
  processing: false,
})

// Visibility options
const visibilityOptions = [
  {
    value: 'public',
    label: 'Public',
    description: 'Visible to all students in course catalog'
  },
  {
    value: 'private',
    label: 'Private',
    description: 'Only visible to you (for testing)'
  },
  {
    value: 'unlisted',
    label: 'Unlisted',
    description: 'Only accessible via direct link'
  }
]

// Computed properties
const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

// Methods
const handleThumbnailChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  if (!file.type.match('image.*')) {
    alert('Please select an image file')
    return
  }

  // Validate file size (2MB max)
  if (file.size > 2 * 1024 * 1024) {
    alert('Image size should be less than 2MB')
    return
  }

  form.thumbnail = file
  removeThumbnailFlag.value = false

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    thumbnailPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const removeThumbnail = () => {
  form.thumbnail = null
  thumbnailPreview.value = null
  removeThumbnailFlag.value = true
  if (thumbnailInput.value) {
    thumbnailInput.value.value = ''
  }
}

const addObjective = () => {
  form.learning_objectives.push('')
}

const removeObjective = (index) => {
  if (form.learning_objectives.length > 1) {
    form.learning_objectives.splice(index, 1)
  }
}

const addPrerequisite = () => {
  form.prerequisites.push('')
}

const removePrerequisite = (index) => {
  if (form.prerequisites.length > 1) {
    form.prerequisites.splice(index, 1)
  }
}

const resetForm = () => {
  // Reset to original values
  form.title = props.course.title
  form.subject = props.course.subject
  form.description = props.course.description || ''
  form.exam_board_id = props.course.exam_board_id
  form.level = props.course.level
  form.status = props.course.status
  form.visibility = props.course.visibility
  form.enrollment_limit = props.course.enrollment_limit
  form.estimated_duration_hours = props.course.estimated_duration_hours || 40
  form.target_completion_date = props.course.target_completion_date
    ? new Date(props.course.target_completion_date).toISOString().split('T')[0]
    : ''
  form.learning_objectives = Array.isArray(props.course.learning_objectives)
    ? [...props.course.learning_objectives]
    : (props.course.learning_objectives ? [props.course.learning_objectives] : [''])
  form.prerequisites = Array.isArray(props.course.prerequisites)
    ? [...props.course.prerequisites]
    : (props.course.prerequisites ? [props.course.prerequisites] : [''])
  form.thumbnail = null
  thumbnailPreview.value = null
  removeThumbnailFlag.value = false
  form.errors = {}
}

const submit = async () => {
  form.processing = true

  try {
    // Create FormData for file upload
    const formData = new FormData()

    // Add all form fields
    Object.keys(form).forEach(key => {
      if (key === 'thumbnail' && form[key]) {
        formData.append(key, form[key])
      } else if (key === 'thumbnail' && removeThumbnailFlag.value) {
        // Handle thumbnail removal
        formData.append('remove_thumbnail', '1')
      } else if (key === 'learning_objectives' || key === 'prerequisites') {
        // Handle arrays
        const filteredArray = form[key].filter(item => item && item.trim() !== '')
        if (filteredArray.length > 0) {
          filteredArray.forEach((item, index) => {
            formData.append(`${key}[${index}]`, item)
          })
        }
      } else if (key === 'exam_board_id' && form[key] === null) {
        formData.append(key, '')
      } else if (key === 'enrollment_limit' && form[key] === '') {
        formData.append(key, '')
      } else if (key !== 'errors' && key !== 'processing' && form[key] !== null && form[key] !== undefined) {
        formData.append(key, form[key])
      }
    })

    // Submit the form
    await router.post(route('admin.courses.update', props.course.id), formData, {
      preserveScroll: true,
      onSuccess: () => {
        form.processing = false
        page.props.flash.success = 'Course updated successfully!'
      },
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
    })

  } catch (error) {
    form.processing = false
    console.error('Course update failed:', error)
    page.props.flash.error = error.message || 'An error occurred while updating the course.'
  }
}

const saveDraft = async () => {
  form.processing = true

  try {
    const formData = new FormData()

    // Add basic fields for draft
    Object.keys(form).forEach(key => {
      if (key === 'thumbnail' && form[key]) {
        formData.append(key, form[key])
      } else if (key === 'learning_objectives' || key === 'prerequisites') {
        const filteredArray = form[key].filter(item => item && item.trim() !== '')
        if (filteredArray.length > 0) {
          filteredArray.forEach((item, index) => {
            formData.append(`${key}[${index}]`, item)
          })
        }
      } else if (key !== 'errors' && key !== 'processing' && key !== 'status' && form[key] !== null && form[key] !== undefined) {
        formData.append(key, form[key])
      }
    })

    // Set status to draft
    formData.append('status', 'draft')

    await router.post(route('admin.courses.update', props.course.id), formData, {
      preserveScroll: true,
      onSuccess: () => {
        form.processing = false
        page.props.flash.success = 'Course saved as draft!'
      },
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
    })

  } catch (error) {
    form.processing = false
    console.error('Draft save failed:', error)
    page.props.flash.error = 'Failed to save draft. Please try again.'
  }
}

const regenerateContent = async () => {
  showRegenerateModal.value = false

  try {
    await router.post(route('admin.catelog.courses.regenerate', props.course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Course content regenerated successfully!'
      },
      onError: (errors) => {
        page.props.flash.error = errors.message || 'Failed to regenerate content'
      },
    })
  } catch (error) {
    console.error('Regeneration failed:', error)
    page.props.flash.error = 'Failed to regenerate content. Please try again.'
  }
}
</script>

<style scoped>
/* Smooth transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
