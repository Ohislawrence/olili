<!-- resources/js/Pages/Admin/Specializations/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit ${specialization.name}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Specialization</h1>
              <p class="text-gray-600">Update learning track details</p>
            </div>
            <div class="flex space-x-2">
              <Link
                :href="route('admin.specializations.show', specialization.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <EyeIcon class="h-4 w-4 mr-2" />
                View
              </Link>
              <Link
                :href="route('admin.specializations.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Back to List
              </Link>
            </div>
          </div>
        </div>

        <!-- Status Alert -->
        <div v-if="specialization.status === 'active' && specialization.is_public"
             class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
          <div class="flex items-center">
            <CheckCircleIcon class="h-5 w-5 text-green-600 mr-3" />
            <div>
              <h3 class="text-sm font-medium text-green-800">This specialization is published</h3>
              <p class="text-sm text-green-700 mt-1">Changes will be visible to students immediately.</p>
            </div>
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
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Short Description -->
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Short Description *
                  </label>
                  <textarea
                    v-model="form.short_description"
                    rows="2"
                    required
                    maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
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
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
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
                    />
                  </div>
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
                <!-- Current Thumbnail -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Current Thumbnail</label>
                  <div v-if="specialization.thumbnail_url" class="mt-2">
                    <img :src="specialization.thumbnail_url" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                    <div class="flex items-center justify-between mt-2">
                      <span class="text-sm text-gray-500">Current image</span>
                      <button
                        type="button"
                        @click="removeThumbnail = true"
                        class="text-sm text-red-600 hover:text-red-900"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                  <div v-else class="mt-2 p-4 border border-dashed border-gray-300 rounded-lg text-center">
                    <PhotoIcon class="h-8 w-8 text-gray-400 mx-auto" />
                    <p class="text-sm text-gray-500 mt-2">No thumbnail uploaded</p>
                  </div>

                  <!-- Upload New Thumbnail -->
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Thumbnail</label>
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
                      <label class="block text-sm font-medium text-gray-700 mb-2">New Preview</label>
                      <img :src="thumbnailPreview" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                    </div>
                  </div>
                </div>

                <!-- Current Banner -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Current Banner</label>
                  <div v-if="specialization.banner_url" class="mt-2">
                    <img :src="specialization.banner_url" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                    <div class="flex items-center justify-between mt-2">
                      <span class="text-sm text-gray-500">Current image</span>
                      <button
                        type="button"
                        @click="removeBanner = true"
                        class="text-sm text-red-600 hover:text-red-900"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                  <div v-else class="mt-2 p-4 border border-dashed border-gray-300 rounded-lg text-center">
                    <PhotoIcon class="h-8 w-8 text-gray-400 mx-auto" />
                    <p class="text-sm text-gray-500 mt-2">No banner uploaded</p>
                  </div>

                  <!-- Upload New Banner -->
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Banner</label>
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
                      <label class="block text-sm font-medium text-gray-700 mb-2">New Preview</label>
                      <img :src="bannerPreview" class="h-32 w-full object-cover rounded-lg border border-gray-200" />
                    </div>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Tags</label>
                <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
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
                <div v-else class="mb-4 p-3 bg-gray-50 rounded-lg">
                  <p class="text-sm text-gray-500">No tags added yet</p>
                </div>

                <!-- Add New Tag -->
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
              </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Update Specialization</h3>
                  <p class="text-sm text-gray-600">Save your changes</p>
                </div>

                <div class="flex space-x-3">
                  <button
                    type="button"
                    @click="confirmDelete"
                    class="px-6 py-3 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors"
                    :disabled="form.processing"
                  >
                    <TrashIcon class="h-4 w-4 mr-2 inline" />
                    Delete
                  </button>
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
                    {{ form.processing ? 'Updating...' : 'Update Specialization' }}
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

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <ExclamationTriangleIcon class="h-6 w-6 text-red-600 mr-3" />
          <h3 class="text-lg font-semibold text-gray-900">Delete Specialization</h3>
        </div>

        <p class="text-gray-700 mb-4">
          Are you sure you want to delete "<strong>{{ specialization.name }}</strong>"? This action cannot be undone.
        </p>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
          <h4 class="text-sm font-medium text-red-800 mb-2">Warning:</h4>
          <ul class="text-sm text-red-700 space-y-1">
            <li>• All course associations will be removed</li>
            <li>• All resources will be deleted</li>
            <li>• All student enrollments will be removed</li>
            <li>• This action is permanent</li>
          </ul>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="deleteSpecialization"
            class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors"
            :disabled="deleting"
          >
            <TrashIcon v-if="!deleting" class="h-4 w-4 mr-2 inline" />
            {{ deleting ? 'Deleting...' : 'Delete Permanently' }}
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import {
  ArrowLeftIcon,
  EyeIcon,
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
  ExclamationTriangleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  specialization: Object,
  exam_options: Object,
  career_options: Object,
  level_options: Object,
  status_options: Object,
})

// Form state
const form = useForm({
  name: props.specialization.name,
  short_description: props.specialization.short_description,
  description: props.specialization.description,
  target_exam: props.specialization.target_exam,
  target_career: props.specialization.target_career,
  level: props.specialization.level,
  price: props.specialization.price || 0,
  discount_price: props.specialization.discount_price,
  has_discount: props.specialization.has_discount,
  learning_objectives: props.specialization.learning_objectives?.length ? props.specialization.learning_objectives : [''],
  prerequisites: props.specialization.prerequisites?.length ? props.specialization.prerequisites : [''],
  target_skills: props.specialization.target_skills?.length ? props.specialization.target_skills : [''],
  career_opportunities: props.specialization.career_opportunities?.length ? props.specialization.career_opportunities : [''],
  min_electives: props.specialization.min_electives || 0,
  max_electives: props.specialization.max_electives,
  is_featured: props.specialization.is_featured,
  is_public: props.specialization.is_public,
  status: props.specialization.status,
  thumbnail: null,
  banner: null,
  tags: props.specialization.tags?.map(t => t.tag) || [],
})

// Preview states
const thumbnailPreview = ref(null)
const bannerPreview = ref(null)
const newTag = ref('')
const removeThumbnail = ref(false)
const removeBanner = ref(false)
const showDeleteModal = ref(false)
const deleting = ref(false)

// Computed properties
const hasErrors = computed(() => Object.keys(form.errors).length > 0)

// Watch for discount toggle
watch(() => form.has_discount, (newValue) => {
  if (!newValue) {
    form.discount_price = null
  }
})

// Form submission
const submitForm = () => {
  // Add flags for image removal
  if (removeThumbnail.value) {
    form.thumbnail = 'REMOVE'
  }
  if (removeBanner.value) {
    form.banner = 'REMOVE'
  }

  form.put(route('admin.specializations.update', props.specialization.id), {
    onSuccess: () => {
      // Reset previews and removal flags
      thumbnailPreview.value = null
      bannerPreview.value = null
      removeThumbnail.value = false
      removeBanner.value = false
    },
  })
}

// Handle thumbnail upload
const handleThumbnailUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 2 * 1024 * 1024) {
    alert('Thumbnail image must be less than 2MB')
    return
  }

  form.thumbnail = file
  removeThumbnail.value = false

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

  if (file.size > 5 * 1024 * 1024) {
    alert('Banner image must be less than 5MB')
    return
  }

  form.banner = file
  removeBanner.value = false

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

// Delete functionality
const confirmDelete = () => {
  // Check if there are active enrollments
  if (props.specialization.enrollment_count > 0) {
    alert(`Cannot delete specialization with ${props.specialization.enrollment_count} active enrollments. Please archive it instead.`)
    return
  }

  showDeleteModal.value = true
}

const deleteSpecialization = () => {
  deleting.value = true
  router.delete(route('admin.specializations.destroy', props.specialization.id), {
    onFinish: () => {
      deleting.value = false
      showDeleteModal.value = false
    },
  })
}
</script>

<style scoped>
input[type="file"]::-webkit-file-upload-button {
  cursor: pointer;
}

input[type="file"]::-ms-browse {
  cursor: pointer;
}
</style>
