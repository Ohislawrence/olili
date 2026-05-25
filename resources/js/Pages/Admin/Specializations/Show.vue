<!-- resources/js/Pages/Admin/Specializations/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`${specialization.name} - Specialization`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex justify-between items-start">
            <div class="flex items-start space-x-4">
              <!-- Thumbnail -->
              <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-xl overflow-hidden border border-gray-200">
                  <img
                    v-if="specialization.thumbnail_url"
                    :src="specialization.thumbnail_url"
                    :alt="specialization.name"
                    class="h-full w-full object-cover"
                  />
                  <div v-else class="h-full w-full bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                    <AcademicCapIcon class="h-8 w-8 text-blue-600" />
                  </div>
                </div>
              </div>

              <!-- Info -->
              <div>
                <div class="flex items-center space-x-2 mb-2">
                  <h1 class="text-2xl font-bold text-gray-900">{{ specialization.name }}</h1>
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                      getStatusClass(specialization.status)
                    ]"
                  >
                    {{ specialization.status }}
                  </span>
                  <span
                    v-if="specialization.is_featured"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                  >
                    Featured
                  </span>
                  <span
                    v-if="!specialization.is_public"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    Private
                  </span>
                </div>
                <p class="text-gray-600 mb-3">{{ specialization.short_description }}</p>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                  <span class="flex items-center">
                    <AcademicCapIcon class="h-4 w-4 mr-1" />
                    {{ specialization.courses.length }} courses
                  </span>
                  <span class="flex items-center">
                    <ClockIcon class="h-4 w-4 mr-1" />
                    {{ specialization.total_duration }} hours
                  </span>
                  <span class="flex items-center">
                    <UsersIcon class="h-4 w-4 mr-1" />
                    {{ specialization.enrollment_count }} enrolled
                  </span>
                  <span class="flex items-center">
                    <CheckCircleIcon class="h-4 w-4 mr-1" />
                    {{ specialization.completion_count }} completed
                  </span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <Link
                :href="route('admin.specializations.edit', specialization.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit
              </Link>
              <button
                v-if="specialization.status === 'draft'"
                @click="publishSpecialization"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
              >
                <CheckCircleIcon class="h-4 w-4 mr-2" />
                Publish
              </button>
              <button
                v-else-if="specialization.status === 'active'"
                @click="unpublishSpecialization"
                class="inline-flex items-center px-4 py-2 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition-colors"
              >
                <XCircleIcon class="h-4 w-4 mr-2" />
                Unpublish
              </button>
              <button
                @click="toggleFeatured"
                class="inline-flex items-center px-4 py-2 border border-purple-300 text-purple-700 font-medium rounded-lg hover:bg-purple-50 transition-colors"
              >
                <StarIcon class="h-4 w-4 mr-2" />
                {{ specialization.is_featured ? 'Unfeature' : 'Feature' }}
              </button>
            </div>
          </div>

          <!-- Navigation Tabs -->
          <div class="mt-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div>
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
              <div class="prose max-w-none">
                <p class="text-gray-700">{{ specialization.description }}</p>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h4 class="font-medium text-gray-900 mb-2">Enrollment Analytics</h4>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Total Enrollments</span>
                    <span class="font-semibold">{{ enrollment_stats.total }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Active Students</span>
                    <span class="font-semibold">{{ enrollment_stats.active }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Completed</span>
                    <span class="font-semibold">{{ enrollment_stats.completed }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Completion Rate</span>
                    <span class="font-semibold">{{ enrollment_stats.completion_rate }}%</span>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h4 class="font-medium text-gray-900 mb-2">Course Structure</h4>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Total Courses</span>
                    <span class="font-semibold">{{ specialization.courses.length }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Required Courses</span>
                    <span class="font-semibold">{{ specialization.required_courses_count }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Elective Courses</span>
                    <span class="font-semibold">{{ specialization.elective_courses_count }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Total Duration</span>
                    <span class="font-semibold">{{ specialization.total_duration }} hours</span>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h4 class="font-medium text-gray-900 mb-2">Target Information</h4>
                <div class="space-y-2">
                  <div v-if="specialization.target_exam" class="flex justify-between">
                    <span class="text-gray-600">Target Exam</span>
                    <span class="font-semibold">{{ specialization.target_exam }}</span>
                  </div>
                  <div v-if="specialization.target_career" class="flex justify-between">
                    <span class="text-gray-600">Career Path</span>
                    <span class="font-semibold">{{ specialization.target_career }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Level</span>
                    <span class="font-semibold capitalize">{{ specialization.level }}</span>
                  </div>
                  <div v-if="specialization.price" class="flex justify-between">
                    <span class="text-gray-600">Price</span>
                    <span class="font-semibold">
                      <template v-if="specialization.has_discount">
                        <span class="line-through text-gray-400 mr-2">₦{{ specialization.formatted_price.original }}</span>
                        <span class="text-green-600">₦{{ specialization.formatted_price.discount }}</span>
                      </template>
                      <template v-else>
                        ₦{{ specialization.formatted_price?.original }}
                      </template>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Learning Objectives -->
            <div v-if="specialization.learning_objectives?.length" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Learning Objectives</h3>
              <ul class="space-y-2">
                <li v-for="(objective, index) in specialization.learning_objectives" :key="index" class="flex items-start">
                  <CheckCircleIcon class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" />
                  <span class="text-gray-700">{{ objective }}</span>
                </li>
              </ul>
            </div>

            <!-- Target Skills -->
            <div v-if="specialization.target_skills?.length" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills You'll Gain</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(skill, index) in specialization.target_skills"
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                >
                  {{ skill }}
                </span>
              </div>
            </div>
          </div>

          <!-- Courses Tab -->
          <div v-if="activeTab === 'courses'" class="space-y-6">
            <!-- Add Course Form -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Course to Track</h3>
              <div class="space-y-4">
                <!-- Course Search -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Search Courses</label>
                  <div class="flex space-x-2">
                    <input
                      v-model="courseSearchQuery"
                      type="text"
                      placeholder="Search courses by title, subject..."
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      @input="searchCourses"
                    />
                    <select
                      v-model="newCourse.is_required"
                      class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                      <option :value="true">Required</option>
                      <option :value="false">Elective</option>
                    </select>
                  </div>

                  <!-- Search Results -->
                  <div v-if="searchResults.length > 0" class="mt-2 border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                    <div
                      v-for="course in searchResults"
                      :key="course.id"
                      @click="selectCourse(course)"
                      class="px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                    >
                      <div class="font-medium">{{ course.title }}</div>
                      <div class="text-sm text-gray-500">{{ course.subject }} • {{ course.estimated_duration_hours }}h • {{ course.level }}</div>
                    </div>
                  </div>

                  <div v-else-if="courseSearchQuery && !isSearching" class="mt-2 text-sm text-gray-500">
                    No courses found. Try a different search.
                  </div>
                </div>

                <!-- Selected Course Preview -->
                <div v-if="selectedCourse" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                  <div class="flex justify-between items-center">
                    <div>
                      <h4 class="font-medium text-gray-900">{{ selectedCourse.title }}</h4>
                      <p class="text-sm text-gray-500">{{ selectedCourse.subject }} • {{ selectedCourse.estimated_duration_hours }}h</p>
                    </div>
                    <button
                      @click="addSelectedCourse"
                      class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                      :disabled="addingCourse"
                    >
                      {{ addingCourse ? 'Adding...' : 'Add Course' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course List -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Sequence</h3>
                <p class="text-sm text-gray-600">Drag to reorder courses</p>
              </div>

              <!-- Required Courses -->
              <div v-if="requiredCourses.length > 0" class="border-b border-gray-200">
                <div class="px-6 py-3 bg-gray-50">
                  <h4 class="font-medium text-gray-900">Required Courses</h4>
                </div>
                <draggable
                  v-model="requiredCourses"
                  :group="{ name: 'courses' }"
                  item-key="id"
                  @end="updateCourseOrder"
                  class="divide-y divide-gray-200"
                >
                  <template #item="{ element: course, index }">
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                          <div class="flex items-center space-x-2">
                            <GripVerticalIcon class="h-5 w-5 text-gray-400 cursor-move" />
                            <span class="font-medium text-gray-900">{{ index + 1 }}</span>
                          </div>

                          <div class="h-10 w-10 flex-shrink-0">
                            <div class="h-full w-full rounded bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                              <AcademicCapIcon class="h-5 w-5 text-blue-600" />
                            </div>
                          </div>

                          <div>
                            <div class="font-medium text-gray-900">{{ course.title }}</div>
                            <div class="text-sm text-gray-500">
                              {{ course.subject }} • {{ course.estimated_duration_hours }}h
                              <span v-if="course.pivot.recommended_weeks"> • {{ course.pivot.recommended_weeks }} weeks</span>
                            </div>
                          </div>

                          <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                            Required
                          </span>
                        </div>

                        <div class="flex items-center space-x-2">
                          <button
                            @click="editCourseSettings(course)"
                            class="text-gray-600 hover:text-gray-900 p-1"
                          >
                            <Cog6ToothIcon class="h-5 w-5" />
                          </button>
                          <button
                            @click="removeCourse(course)"
                            class="text-red-600 hover:text-red-900 p-1"
                          >
                            <TrashIcon class="h-5 w-5" />
                          </button>
                        </div>
                      </div>

                      <div v-if="course.pivot.notes" class="mt-2 text-sm text-gray-600">
                        <span class="font-medium">Notes:</span> {{ course.pivot.notes }}
                      </div>
                    </div>
                  </template>
                </draggable>
              </div>

              <!-- Elective Courses -->
              <div v-if="electiveCourses.length > 0">
                <div class="px-6 py-3 bg-gray-50">
                  <h4 class="font-medium text-gray-900">
                    Elective Courses (Choose {{ specialization.min_electives }} to {{ specialization.max_electives || specialization.elective_courses_count }})
                  </h4>
                </div>
                <draggable
                  v-model="electiveCourses"
                  :group="{ name: 'courses' }"
                  item-key="id"
                  @end="updateCourseOrder"
                  class="divide-y divide-gray-200"
                >
                  <template #item="{ element: course, index }">
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                          <div class="flex items-center space-x-2">
                            <GripVerticalIcon class="h-5 w-5 text-gray-400 cursor-move" />
                            <span class="font-medium text-gray-900">{{ requiredCourses.length + index + 1 }}</span>
                          </div>

                          <div class="h-10 w-10 flex-shrink-0">
                            <div class="h-full w-full rounded bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                              <AcademicCapIcon class="h-5 w-5 text-blue-600" />
                            </div>
                          </div>

                          <div>
                            <div class="font-medium text-gray-900">{{ course.title }}</div>
                            <div class="text-sm text-gray-500">
                              {{ course.subject }} • {{ course.estimated_duration_hours }}h
                              <span v-if="course.pivot.recommended_weeks"> • {{ course.pivot.recommended_weeks }} weeks</span>
                            </div>
                          </div>

                          <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            Elective
                          </span>
                        </div>

                        <div class="flex items-center space-x-2">
                          <button
                            @click="toggleCourseRequired(course)"
                            class="text-green-600 hover:text-green-900 text-sm font-medium"
                          >
                            Make Required
                          </button>
                          <button
                            @click="editCourseSettings(course)"
                            class="text-gray-600 hover:text-gray-900 p-1"
                          >
                            <Cog6ToothIcon class="h-5 w-5" />
                          </button>
                          <button
                            @click="removeCourse(course)"
                            class="text-red-600 hover:text-red-900 p-1"
                          >
                            <TrashIcon class="h-5 w-5" />
                          </button>
                        </div>
                      </div>

                      <div v-if="course.pivot.notes" class="mt-2 text-sm text-gray-600">
                        <span class="font-medium">Notes:</span> {{ course.pivot.notes }}
                      </div>
                    </div>
                  </template>
                </draggable>
              </div>
            </div>
          </div>

          <!-- Resources Tab -->
          <div v-if="activeTab === 'resources'" class="space-y-6">
            <!-- Add Resource Form -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Resource</h3>
              <form @submit.prevent="addResource" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input
                      v-model="newResource.title"
                      type="text"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select
                      v-model="newResource.type"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                      <option value="">Select Type</option>
                      <option v-for="(label, value) in resource_types" :key="value" :value="value">{{ label }}</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="newResource.description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                    <input
                      type="file"
                      @change="handleResourceFile"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">External URL</label>
                    <input
                      v-model="newResource.external_url"
                      type="url"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    />
                  </div>
                </div>

                <div class="flex items-center space-x-4">
                  <label class="flex items-center">
                    <input
                      v-model="newResource.is_free"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">Free Resource</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="newResource.is_active"
                      type="checkbox"
                      checked
                      class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                  </label>
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    :disabled="addingResource"
                  >
                    {{ addingResource ? 'Adding...' : 'Add Resource' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Resources List -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Exam Resources</h3>
              </div>

              <draggable
                v-model="resources"
                item-key="id"
                @end="updateResourceOrder"
                class="divide-y divide-gray-200"
              >
                <template #item="{ element: resource, index }">
                  <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <GripVerticalIcon class="h-5 w-5 text-gray-400 cursor-move" />

                        <div class="flex-shrink-0">
                          <div :class="[
                            'h-10 w-10 rounded-lg flex items-center justify-center',
                            getResourceTypeClass(resource.type)
                          ]">
                            <component :is="getResourceIcon(resource.type)" class="h-5 w-5 text-white" />
                          </div>
                        </div>

                        <div class="flex-1">
                          <div class="font-medium text-gray-900">{{ resource.title }}</div>
                          <div class="text-sm text-gray-500">{{ resource.description }}</div>
                          <div class="flex items-center space-x-2 mt-1">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-800">
                              {{ resource_types[resource.type] }}
                            </span>
                            <span v-if="!resource.is_free" class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                              Premium
                            </span>
                            <span v-if="!resource.is_active" class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-800">
                              Inactive
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="flex items-center space-x-2">
                        <a
                          v-if="resource.external_url"
                          :href="resource.external_url"
                          target="_blank"
                          class="text-blue-600 hover:text-blue-900"
                        >
                          Open
                        </a>
                        <button
                          @click="editResource(resource)"
                          class="text-gray-600 hover:text-gray-900 p-1"
                        >
                          <PencilIcon class="h-5 w-5" />
                        </button>
                        <button
                          @click="deleteResource(resource)"
                          class="text-red-600 hover:text-red-900 p-1"
                        >
                          <TrashIcon class="h-5 w-5" />
                        </button>
                      </div>
                    </div>
                  </div>
                </template>
              </draggable>

              <div v-if="resources.length === 0" class="px-6 py-8 text-center">
                <DocumentIcon class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                <h4 class="text-lg font-medium text-gray-900 mb-2">No Resources Added</h4>
                <p class="text-gray-500">Add past questions, study guides, and other resources for this track.</p>
              </div>
            </div>
          </div>

          <!-- Enrollments Tab -->
          <div v-if="activeTab === 'enrollments'" class="space-y-6">
            <!-- Enrollment Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="text-center">
                  <div class="text-3xl font-bold text-blue-600">{{ enrollment_stats.total }}</div>
                  <div class="text-sm text-gray-500">Total Enrolled</div>
                </div>
              </div>
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="text-center">
                  <div class="text-3xl font-bold text-green-600">{{ enrollment_stats.active }}</div>
                  <div class="text-sm text-gray-500">Active</div>
                </div>
              </div>
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="text-center">
                  <div class="text-3xl font-bold text-purple-600">{{ enrollment_stats.completed }}</div>
                  <div class="text-sm text-gray-500">Completed</div>
                </div>
              </div>
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="text-center">
                  <div class="text-3xl font-bold text-amber-600">{{ enrollment_stats.completion_rate }}%</div>
                  <div class="text-sm text-gray-500">Completion Rate</div>
                </div>
              </div>
            </div>

            <!-- Bulk Enrollment -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Bulk Enroll Students</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Add Students</label>
                  <textarea
                    v-model="bulkEnrollment.emails"
                    rows="4"
                    placeholder="Enter student emails, one per line"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  ></textarea>
                  <p class="text-sm text-gray-500 mt-1">Students must have existing accounts</p>
                </div>
                <div class="flex justify-end">
                  <button
                    @click="processBulkEnrollment"
                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    :disabled="bulkEnrollment.processing"
                  >
                    {{ bulkEnrollment.processing ? 'Processing...' : 'Enroll Students' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Enrollments Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-900">Student Enrollments</h3>
                  <div class="text-sm text-gray-500">
                    Showing {{ enrollments.data.length }} of {{ enrollments.total }}
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="enrollment in enrollments.data" :key="enrollment.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="h-10 w-10 flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                              <UserIcon class="h-5 w-5 text-blue-600" />
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="font-medium text-gray-900">{{ enrollment.user.name }}</div>
                            <div class="text-sm text-gray-500">{{ enrollment.user.email }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                          getEnrollmentStatusClass(enrollment.status)
                        ]">
                          {{ enrollment.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                          <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div
                              class="bg-green-500 h-2 rounded-full transition-all duration-300"
                              :style="{ width: enrollment.progress_percentage + '%' }"
                            ></div>
                          </div>
                          <span class="text-sm text-gray-700">{{ Math.round(enrollment.progress_percentage) }}%</span>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(enrollment.enrolled_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                          @click="viewStudentProgress(enrollment.user_id)"
                          class="text-blue-600 hover:text-blue-900 mr-3"
                        >
                          View Progress
                        </button>
                        <button
                          @click="removeEnrollment(enrollment)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Remove
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div v-if="enrollments.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                <Pagination :links="enrollments.links" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course Settings Modal -->
    <Modal :show="showCourseSettings" @close="showCourseSettings = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Settings</h3>
        <form @submit.prevent="updateCourseSettings" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Course Type</label>
            <select
              v-model="courseSettings.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            >
              <option :value="true">Required</option>
              <option :value="false">Elective</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="courseSettings.category"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            >
              <option value="core">Core</option>
              <option value="elective">Elective</option>
              <option value="supplementary">Supplementary</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Recommended Weeks</label>
            <input
              v-model="courseSettings.recommended_weeks"
              type="number"
              min="1"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea
              v-model="courseSettings.notes"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showCourseSettings = false"
              class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
              :disabled="updatingCourseSettings"
            >
              {{ updatingCourseSettings ? 'Updating...' : 'Update Settings' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import draggable from 'vuedraggable'
import {
  AcademicCapIcon,
  PencilIcon,
  CheckCircleIcon,
  XCircleIcon,
  StarIcon,
  ClockIcon,
  UsersIcon,
  Cog6ToothIcon,
  TrashIcon,
  DocumentIcon,
  UserIcon,
  DocumentDuplicateIcon,
  BookOpenIcon,
  VideoCameraIcon,
  LinkIcon,
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  specialization: Object,
  available_courses: Array,
  enrollment_stats: Object,
  enrollments: Object,
  exam_options: Object,
  career_options: Object,
  level_options: Object,
  status_options: Object,
  resource_types: Object,
})

// Tab Management
const tabs = [
  { id: 'overview', name: 'Overview' },
  { id: 'courses', name: 'Courses' },
  { id: 'resources', name: 'Resources' },
  { id: 'enrollments', name: 'Enrollments' },
]
const activeTab = ref('overview')

// Course Management
const requiredCourses = ref([...props.specialization.courses.filter(c => c.pivot.is_required)])
const electiveCourses = ref([...props.specialization.courses.filter(c => !c.pivot.is_required)])
const courseSearchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const selectedCourse = ref(null)
const addingCourse = ref(false)

const newCourse = reactive({
  is_required: true,
})

// Resource Management
const resources = ref([...props.specialization.resources])
const newResource = reactive({
  title: '',
  type: '',
  description: '',
  file: null,
  external_url: '',
  is_free: true,
  is_active: true,
})
const addingResource = ref(false)

// Course Settings Modal
const showCourseSettings = ref(false)
const courseSettings = reactive({
  course_id: null,
  is_required: true,
  category: 'core',
  recommended_weeks: null,
  notes: '',
})
const updatingCourseSettings = ref(false)

// Bulk Enrollment
const bulkEnrollment = reactive({
  emails: '',
  processing: false,
})

// Computed Properties
const allCourses = computed(() => [...requiredCourses.value, ...electiveCourses.value])

// Methods
const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    archived: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getEnrollmentStatusClass = (status) => {
  const classes = {
    enrolled: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-purple-100 text-purple-800',
    dropped: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getResourceTypeClass = (type) => {
  const classes = {
    resource: 'bg-blue-500',
    past_question: 'bg-green-500',
    guide: 'bg-purple-500',
    video: 'bg-red-500',
    link: 'bg-amber-500',
  }
  return classes[type] || 'bg-gray-500'
}

const getResourceIcon = (type) => {
  const icons = {
    resource: DocumentDuplicateIcon,
    past_question: DocumentIcon,
    guide: BookOpenIcon,
    video: VideoCameraIcon,
    link: LinkIcon,
  }
  return icons[type] || DocumentIcon
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

// Course Management Methods
const searchCourses = async () => {
  if (courseSearchQuery.value.length < 2) {
    searchResults.value = []
    return
  }

  isSearching.value = true
  try {
    const response = await axios.get(route('admin.courses.search'), {
      params: {
        search: courseSearchQuery.value,
        exclude_specialization: props.specialization.id,
        limit: 10,
      }
    })
    searchResults.value = response.data.courses
  } catch (error) {
    console.error('Search failed:', error)
  } finally {
    isSearching.value = false
  }
}

const selectCourse = (course) => {
  selectedCourse.value = course
  searchResults.value = []
}

const addSelectedCourse = async () => {
  if (!selectedCourse.value) return

  addingCourse.value = true
  try {
    await axios.post(route('admin.specializations.add-course', props.specialization.id), {
      course_id: selectedCourse.value.id,
      is_required: newCourse.is_required,
    })

    // Refresh the page to get updated course list
    router.reload()
  } catch (error) {
    console.error('Failed to add course:', error)
    alert('Failed to add course: ' + error.response?.data?.message || error.message)
  } finally {
    addingCourse.value = false
  }
}

const removeCourse = async (course) => {
  if (!confirm(`Remove "${course.title}" from this track?`)) return

  try {
    await axios.delete(route('admin.specializations.remove-course', {
      specialization: props.specialization.id,
      course: course.id,
    }))

    // Refresh the page
    router.reload()
  } catch (error) {
    console.error('Failed to remove course:', error)
    alert('Failed to remove course: ' + error.response?.data?.message || error.message)
  }
}

const toggleCourseRequired = async (course) => {
  try {
    await axios.put(route('admin.specializations.update-course-settings', {
      specialization: props.specialization.id,
      course: course.id,
    }), {
      is_required: true,
    })

    router.reload()
  } catch (error) {
    console.error('Failed to update course:', error)
  }
}

const updateCourseOrder = async () => {
  const courseIds = allCourses.value.map(c => c.id)

  try {
    await axios.put(route('admin.specializations.update-course-order', props.specialization.id), {
      courses: courseIds,
    })
  } catch (error) {
    console.error('Failed to update course order:', error)
  }
}

const editCourseSettings = (course) => {
  courseSettings.course_id = course.id
  courseSettings.is_required = course.pivot.is_required
  courseSettings.category = course.pivot.category || 'core'
  courseSettings.recommended_weeks = course.pivot.recommended_weeks
  courseSettings.notes = course.pivot.notes || ''
  showCourseSettings.value = true
}

const updateCourseSettings = async () => {
  updatingCourseSettings.value = true
  try {
    await axios.put(route('admin.specializations.update-course-settings', {
      specialization: props.specialization.id,
      course: courseSettings.course_id,
    }), courseSettings)

    showCourseSettings.value = false
    router.reload()
  } catch (error) {
    console.error('Failed to update course settings:', error)
  } finally {
    updatingCourseSettings.value = false
  }
}

// Resource Management Methods
const handleResourceFile = (event) => {
  newResource.file = event.target.files[0]
}

const addResource = async () => {
  addingResource.value = true

  const formData = new FormData()
  formData.append('title', newResource.title)
  formData.append('type', newResource.type)
  formData.append('description', newResource.description || '')
  formData.append('external_url', newResource.external_url || '')
  formData.append('is_free', newResource.is_free)
  formData.append('is_active', newResource.is_active)

  if (newResource.file) {
    formData.append('file', newResource.file)
  }

  try {
    await axios.post(route('admin.specializations.add-resource', props.specialization.id), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })

    // Reset form and reload
    Object.assign(newResource, {
      title: '',
      type: '',
      description: '',
      file: null,
      external_url: '',
      is_free: true,
      is_active: true,
    })
    router.reload()
  } catch (error) {
    console.error('Failed to add resource:', error)
    alert('Failed to add resource: ' + error.response?.data?.message || error.message)
  } finally {
    addingResource.value = false
  }
}

const editResource = (resource) => {
  // Navigate to edit page or open edit modal
  // For simplicity, we'll just alert
  alert('Edit resource functionality would go here')
}

const deleteResource = async (resource) => {
  if (!confirm(`Delete "${resource.title}" resource?`)) return

  try {
    await axios.delete(route('admin.specializations.delete-resource', {
      specialization: props.specialization.id,
      resource: resource.id,
    }))

    router.reload()
  } catch (error) {
    console.error('Failed to delete resource:', error)
    alert('Failed to delete resource: ' + error.response?.data?.message || error.message)
  }
}

const updateResourceOrder = async () => {
  const resourceIds = resources.value.map(r => r.id)

  try {
    await axios.put(route('admin.specializations.reorder-resources', props.specialization.id), {
      resources: resourceIds,
    })
  } catch (error) {
    console.error('Failed to update resource order:', error)
  }
}

// Enrollment Methods
const processBulkEnrollment = async () => {
  const emails = bulkEnrollment.emails
    .split('\n')
    .map(email => email.trim())
    .filter(email => email.length > 0)

  if (emails.length === 0) {
    alert('Please enter at least one email address')
    return
  }

  bulkEnrollment.processing = true

  try {
    // Get user IDs from emails
    const userIds = []
    for (const email of emails) {
      const response = await axios.get(route('admin.users.search'), {
        params: { email }
      })
      if (response.data.users.length > 0) {
        userIds.push(response.data.users[0].id)
      }
    }

    if (userIds.length === 0) {
      alert('No users found with the provided emails')
      return
    }

    // Bulk enroll
    await axios.post(route('admin.specializations.bulk-enroll', props.specialization.id), {
      user_ids: userIds,
    })

    bulkEnrollment.emails = ''
    router.reload()
  } catch (error) {
    console.error('Bulk enrollment failed:', error)
    alert('Failed to enroll users: ' + error.response?.data?.message || error.message)
  } finally {
    bulkEnrollment.processing = false
  }
}

const removeEnrollment = async (enrollment) => {
  if (!confirm(`Remove ${enrollment.user.name} from this track?`)) return

  try {
    await enrollment.delete()
    router.reload()
  } catch (error) {
    console.error('Failed to remove enrollment:', error)
    alert('Failed to remove enrollment: ' + error.response?.data?.message || error.message)
  }
}

const viewStudentProgress = (userId) => {
  // Navigate to student progress page
  router.get(route('admin.users.show', userId))
}

// Specialization Actions
const publishSpecialization = () => {
  if (confirm('Publish this specialization? It will become visible to students.')) {
    router.post(route('admin.specializations.publish', props.specialization.id), {}, {
      onSuccess: () => router.reload()
    })
  }
}

const unpublishSpecialization = () => {
  if (confirm('Unpublish this specialization? It will no longer be visible to students.')) {
    router.post(route('admin.specializations.unpublish', props.specialization.id), {}, {
      onSuccess: () => router.reload()
    })
  }
}

const toggleFeatured = () => {
  router.post(route('admin.specializations.toggle-featured', props.specialization.id), {}, {
    onSuccess: () => router.reload()
  })
}
</script>
