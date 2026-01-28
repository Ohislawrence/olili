<!-- resources/js/Pages/Admin/Specializations/Create.vue -->
<template>
  <AdminLayout>
    <Head title="Create New Specialization" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create New Specialization</h1>
              <p class="text-gray-600">Create a new learning track or bundle</p>
            </div>
            <Link
              :href="route('admin.specializations.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to List
            </Link>
          </div>
        </div>

        <form @submit.prevent="submitForm">
          <div class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                  <InformationCircleIcon class="h-6 w-6 text-blue-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Track Name -->
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Track Name *
                    <span class="text-xs text-gray-500 ml-2">e.g., "Medical Track for JAMB"</span>
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Enter track name"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Short Description -->
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Short Description *
                    <span class="text-xs text-gray-500 ml-2">Brief description (max 500 characters)</span>
                  </label>
                  <textarea
                    v-model="form.short_description"
                    rows="2"
                    required
                    maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Brief description that appears in listings"
                  ></textarea>
                  <div class="flex justify-between mt-1">
                    <p v-if="form.errors.short_description" class="text-sm text-red-600">{{ form.errors.short_description }}</p>
                    <span class="text-xs text-gray-500">{{ form.short_description?.length || 0 }}/500</span>
                  </div>
                </div>

                <!-- Full Description -->
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Full Description *
                    <span class="text-xs text-gray-500 ml-2">Detailed description of the track</span>
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Detailed description of what students will learn"
                  ></textarea>
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <!-- Target Exam -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Target Exam</label>
                  <select
                    v-model="form.target_exam"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Select Exam (Optional)</option>
                    <option v-for="(label, value) in exam_options" :key="value" :value="value">{{ label }}</option>
                  </select>
                </div>

                <!-- Career Path -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Career Path</label>
                  <select
                    v-model="form.target_career"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Select Career Path (Optional)</option>
                    <option v-for="(label, value) in career_options" :key="value" :value="value">{{ label }}</option>
                  </select>
                </div>

                <!-- Level -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                  <select
                    v-model="form.level"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Select Level</option>
                    <option v-for="(label, value) in level_options" :key="value" :value="value">{{ label }}</option>
                  </select>
                  <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">{{ form.errors.level }}</p>
                </div>

                <!-- Status -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                  <select
                    v-model="form.status"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Select Status</option>
                    <option v-for="(label, value) in status_options" :key="value" :value="value">{{ label }}</option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                </div>
              </div>
            </div>

            <!-- Pricing & Visibility Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                  <CurrencyDollarIcon class="h-6 w-6 text-purple-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Pricing & Visibility</h2>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Price (₦)</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500">₦</span>
                    </div>
                    <input
                      v-model="form.price"
                      type="number"
                      min="0"
                      step="0.01"
                      class="pl-8 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      placeholder="0.00"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Leave as 0 for free track</p>
                  <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
                </div>

                <!-- Discount Price -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Discount Price (₦)</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500">₦</span>
                    </div>
                    <input
                      v-model="form.discount_price"
                      type="number"
                      min="0"
                      step="0.01"
                      :disabled="!form.has_discount"
                      class="pl-8 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50 disabled:text-gray-500"
                      placeholder="0.00"
                    />
                  </div>
                </div>

                <!-- Has Discount -->
                <div class="flex items-center">
                  <input
                    v-model="form.has_discount"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                  />
                  <label class="ml-2 text-sm text-gray-700">Enable Discount</label>
                </div>

                <!-- Is Featured -->
                <div class="flex items-center">
                  <input
                    v-model="form.is_featured"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                  />
                  <label class="ml-2 text-sm text-gray-700">Featured Track</label>
                </div>

                <!-- Is Public -->
                <div class="flex items-center">
                  <input
                    v-model="form.is_public"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                  />
                  <label class="ml-2 text-sm text-gray-700">Public Track</label>
                </div>
              </div>
            </div>

            <!-- Learning Objectives Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                  <AcademicCapIcon class="h-6 w-6 text-green-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Learning Objectives</h2>
              </div>

              <div class="space-y-4">
                <div v-for="(objective, index) in form.learning_objectives" :key="index" class="flex items-start">
                  <div class="flex-1">
                    <input
                      v-model="form.learning_objectives[index]"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      :placeholder="`Learning objective ${index + 1}`"
                      maxlength="500"
                    />
                  </div>
                  <button
                    type="button"
                    @click="removeLearningObjective(index)"
                    class="ml-2 p-2 text-red-600 hover:text-red-900"
                    :disabled="form.learning_objectives.length <= 1"
                  >
                    <TrashIcon class="h-5 w-5" />
                  </button>
                </div>

                <button
                  type="button"
                  @click="addLearningObjective"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  Add Learning Objective
                </button>
              </div>
            </div>

            <!-- Skills & Prerequisites Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Target Skills -->
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center mb-6">
                  <div class="p-2 bg-amber-100 rounded-lg mr-3">
                    <LightBulbIcon class="h-6 w-6 text-amber-600" />
                  </div>
                  <h2 class="text-lg font-semibold text-gray-900">Skills Students Will Gain</h2>
                </div>

                <div class="space-y-3">
                  <div v-for="(skill, index) in form.target_skills" :key="index" class="flex items-center">
                    <div class="flex-1">
                      <input
                        v-model="form.target_skills[index]"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="e.g., Critical Thinking, Problem Solving"
                        maxlength="200"
                      />
                    </div>
                    <button
                      type="button"
                      @click="removeTargetSkill(index)"
                      class="ml-2 p-2 text-red-600 hover:text-red-900"
                      :disabled="form.target_skills.length <= 1"
                    >
                      <TrashIcon class="h-5 w-5" />
                    </button>
                  </div>

                  <button
                    type="button"
                    @click="addTargetSkill"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Add Skill
                  </button>
                </div>
              </div>

              <!-- Prerequisites -->
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center mb-6">
                  <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <ShieldCheckIcon class="h-6 w-6 text-blue-600" />
                  </div>
                  <h2 class="text-lg font-semibold text-gray-900">Prerequisites</h2>
                </div>

                <div class="space-y-3">
                  <div v-for="(prerequisite, index) in form.prerequisites" :key="index" class="flex items-center">
                    <div class="flex-1">
                      <input
                        v-model="form.prerequisites[index]"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="e.g., Basic knowledge of mathematics"
                        maxlength="500"
                      />
                    </div>
                    <button
                      type="button"
                      @click="removePrerequisite(index)"
                      class="ml-2 p-2 text-red-600 hover:text-red-900"
                      :disabled="form.prerequisites.length <= 1"
                    >
                      <TrashIcon class="h-5 w-5" />
                    </button>
                  </div>

                  <button
                    type="button"
                    @click="addPrerequisite"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Add Prerequisite
                  </button>
                </div>
              </div>
            </div>

            <!-- Career Opportunities Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                  <BriefcaseIcon class="h-6 w-6 text-indigo-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Career Opportunities</h2>
              </div>

              <div class="space-y-3">
                <div v-for="(opportunity, index) in form.career_opportunities" :key="index" class="flex items-center">
                  <div class="flex-1">
                    <input
                      v-model="form.career_opportunities[index]"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      placeholder="e.g., Medical Doctor, Researcher"
                      maxlength="200"
                    />
                  </div>
                  <button
                    type="button"
                    @click="removeCareerOpportunity(index)"
                    class="ml-2 p-2 text-red-600 hover:text-red-900"
                    :disabled="form.career_opportunities.length <= 1"
                  >
                    <TrashIcon class="h-5 w-5" />
                  </button>
                </div>

                <button
                  type="button"
                  @click="addCareerOpportunity"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  Add Career Opportunity
                </button>
              </div>
            </div>

            <!-- Elective Settings Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                  <AdjustmentsHorizontalIcon class="h-6 w-6 text-yellow-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Elective Course Settings</h2>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Minimum Electives -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Electives</label>
                  <input
                    v-model="form.min_electives"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="0"
                  />
                  <p class="mt-1 text-xs text-gray-500">Minimum number of elective courses students must take</p>
                </div>

                <!-- Maximum Electives -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Electives</label>
                  <input
                    v-model="form.max_electives"
                    type="number"
                    min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Leave empty for unlimited"
                  />
                  <p class="mt-1 text-xs text-gray-500">Maximum elective courses students can take (optional)</p>
                </div>
              </div>
            </div>

            <!-- Media Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-pink-100 rounded-lg mr-3">
                  <PhotoIcon class="h-6 w-6 text-pink-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Media</h2>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thumbnail Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Image</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                    <div class="space-y-1 text-center">
                      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
                      <div class="flex text-sm text-gray-600">
                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                          <span>Upload a file</span>
                          <input
                            type="file"
                            @change="handleThumbnailUpload"
                            accept="image/*"
                            class="sr-only"
                          />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                  </div>

                  <!-- Preview -->
                  <div v-if="thumbnailPreview" class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <img :src="thumbnailPreview" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                  </div>
                </div>

                <!-- Banner Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                    <div class="space-y-1 text-center">
                      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
                      <div class="flex text-sm text-gray-600">
                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                          <span>Upload a file</span>
                          <input
                            type="file"
                            @change="handleBannerUpload"
                            accept="image/*"
                            class="sr-only"
                          />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    </div>
                  </div>

                  <!-- Preview -->
                  <div v-if="bannerPreview" class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <img :src="bannerPreview" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Tags Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="p-2 bg-teal-100 rounded-lg mr-3">
                  <TagIcon class="h-6 w-6 text-teal-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Tags</h2>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Tags</label>
                <div class="flex">
                  <input
                    v-model="newTag"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="e.g., science, medicine, jamb"
                    @keyup.enter="addTag"
                  />
                  <button
                    type="button"
                    @click="addTag"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-r-lg hover:bg-blue-700 transition-colors"
                  >
                    Add
                  </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Press Enter or click Add to include tags</p>

                <!-- Tags List -->
                <div v-if="form.tags.length > 0" class="mt-4">
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(tag, index) in form.tags"
                      :key="index"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                    >
                      {{ tag }}
                      <button
                        type="button"
                        @click="removeTag(index)"
                        class="ml-1.5 text-blue-600 hover:text-blue-900"
                      >
                        &times;
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Create Specialization</h3>
                  <p class="text-sm text-gray-600">Review and create your new learning track</p>
                </div>

                <div class="flex space-x-3">
                  <Link
                    :href="route('admin.specializations.index')"
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                    :disabled="form.processing"
                  >
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <CheckIcon v-if="!form.processing" class="h-5 w-5 mr-2" />
                    <svg v-else class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Creating...' : 'Create Specialization' }}
                  </button>
                </div>
              </div>

              <!-- Validation Summary -->
              <div v-if="hasErrors" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h4>
                <ul class="text-sm text-red-700 space-y-1">
                  <li v-for="(error, field) in form.errors" :key="field">
                    • {{ field.replace('_', ' ') }}: {{ error }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  InformationCircleIcon,
  AcademicCapIcon,
  CurrencyDollarIcon,
  LightBulbIcon,
  ShieldCheckIcon,
  BriefcaseIcon,
  AdjustmentsHorizontalIcon,
  PhotoIcon,
  TagIcon,
  CheckIcon,
  PlusIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  exam_options: Object,
  career_options: Object,
  level_options: Object,
  status_options: Object,
})

// Form state
const form = useForm({
  name: '',
  short_description: '',
  description: '',
  target_exam: '',
  target_career: '',
  level: '',
  price: 0,
  discount_price: null,
  has_discount: false,
  learning_objectives: [''],
  prerequisites: [''],
  target_skills: [''],
  career_opportunities: [''],
  min_electives: 0,
  max_electives: null,
  is_featured: false,
  is_public: false,
  status: 'draft',
  thumbnail: null,
  banner: null,
  tags: [],
})

// Preview states
const thumbnailPreview = ref(null)
const bannerPreview = ref(null)
const newTag = ref('')

// Computed properties
const hasErrors = computed(() => Object.keys(form.errors).length > 0)

// Form submission
const submitForm = () => {
  form.post(route('admin.specializations.store'), {
    onSuccess: () => {
      // Reset previews
      thumbnailPreview.value = null
      bannerPreview.value = null
      newTag.value = ''
    },
  })
}

// Handle thumbnail upload
const handleThumbnailUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 2 * 1024 * 1024) { // 2MB limit
    alert('Thumbnail image must be less than 2MB')
    return
  }

  form.thumbnail = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    thumbnailPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Handle banner upload
const handleBannerUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) { // 5MB limit
    alert('Banner image must be less than 5MB')
    return
  }

  form.banner = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    bannerPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Dynamic field management
const addLearningObjective = () => {
  form.learning_objectives.push('')
}

const removeLearningObjective = (index) => {
  if (form.learning_objectives.length > 1) {
    form.learning_objectives.splice(index, 1)
  }
}

const addTargetSkill = () => {
  form.target_skills.push('')
}

const removeTargetSkill = (index) => {
  if (form.target_skills.length > 1) {
    form.target_skills.splice(index, 1)
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

const addCareerOpportunity = () => {
  form.career_opportunities.push('')
}

const removeCareerOpportunity = (index) => {
  if (form.career_opportunities.length > 1) {
    form.career_opportunities.splice(index, 1)
  }
}

// Tag management
const addTag = () => {
  const tag = newTag.value.trim().toLowerCase()
  if (tag && !form.tags.includes(tag) && tag.length <= 50) {
    form.tags.push(tag)
    newTag.value = ''
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}
</script>

<style scoped>
/* Custom styles for file upload area */
input[type="file"]::-webkit-file-upload-button {
  cursor: pointer;
}

input[type="file"]::-ms-browse {
  cursor: pointer;
}
</style>
