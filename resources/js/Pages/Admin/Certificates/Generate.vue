<!-- resources/js/Pages/Admin/Certificates/Generate.vue -->
<template>
  <AdminLayout>
    <Head title="Generate Certificate" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <div class="flex items-center space-x-2 mb-2">
                <Link
                  :href="route('admin.users.certificates', user.id)"
                  class="text-gray-500 hover:text-gray-700"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </Link>
                <h1 class="text-3xl font-bold text-gray-900">Generate Certificate</h1>
              </div>
              <p class="text-gray-600">Create a new certificate for {{ user.name }}</p>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Certificate Details</h3>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- User Info -->
            <div class="p-4 bg-gray-50 rounded-lg">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Student Information</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Name</p>
                  <p class="text-lg font-semibold text-gray-900">{{ user.name }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="text-lg font-semibold text-gray-900">{{ user.email }}</p>
                </div>
              </div>
            </div>

            <!-- Course Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Course *
                <span class="text-xs text-gray-500 ml-1">(Only completed courses are shown)</span>
              </label>
              <div v-if="completedCourses.length > 0" class="space-y-3">
                <div v-for="course in completedCourses" :key="course.id" class="flex items-center">
                  <input
                    :id="`course-${course.id}`"
                    v-model="form.course_id"
                    type="radio"
                    :value="course.id"
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    :disabled="course.has_certificate"
                  />
                  <label :for="`course-${course.id}`" class="ml-3 cursor-pointer">
                    <span class="block text-sm font-medium text-gray-900">{{ course.title }}</span>
                    <span class="block text-xs text-gray-500">
                      Completed: {{ formatDate(course.completed_at) }} |
                      Progress: {{ course.progress_percentage }}%
                      <span v-if="course.has_certificate" class="text-amber-600 ml-2">
                        (Certificate already exists)
                      </span>
                      <span v-else-if="!course.can_generate" class="text-red-600 ml-2">
                        (Not eligible - Progress: {{ course.progress_percentage }}%)
                      </span>
                    </span>
                  </label>
                </div>
              </div>
              <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No completed courses</h3>
                <p class="mt-1 text-sm text-gray-500">
                  The user must complete a course to receive a certificate.
                </p>
              </div>
              <p v-if="completedCourses.length === 0" class="text-sm text-gray-500 mt-2">
                No completed courses found. The user must complete a course to receive a certificate.
              </p>
            </div>

            <!-- Auto-generate option -->
            <div v-if="eligibleCourses.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-sm font-medium text-blue-900">Auto-generate Eligible Certificate</h4>
                  <p class="text-xs text-blue-700 mt-1">
                    {{ eligibleCourses.length }} course(s) are eligible for certificate generation
                  </p>
                </div>
                <button
                  type="button"
                  @click="autoGenerateCertificate"
                  class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  <BoltIcon class="h-4 w-4 mr-2" />
                  Auto-generate
                </button>
              </div>
            </div>

            <!-- Issuer Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Issuing Organization</label>
              <div class="space-y-3">
                <div class="flex items-center">
                  <input
                    id="issuer-platform"
                    v-model="form.organization_id"
                    type="radio"
                    value=""
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                  />
                  <label for="issuer-platform" class="ml-3 cursor-pointer">
                    <span class="block text-sm font-medium text-gray-900">Olilearn AI Learning Platform</span>
                    <span class="block text-xs text-gray-500">Default platform certificate</span>
                  </label>
                </div>
                <div v-for="org in organizations" :key="org.id" class="flex items-center">
                  <input
                    :id="`org-${org.id}`"
                    v-model="form.organization_id"
                    type="radio"
                    :value="org.id"
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                  />
                  <label :for="`org-${org.id}`" class="ml-3 cursor-pointer">
                    <span class="block text-sm font-medium text-gray-900">{{ org.name }}</span>
                    <span v-if="org.is_verified" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                      Verified
                    </span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Certificate Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Certificate Title *</label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="e.g., Certificate of Completion"
                />
              </div>
              <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">Issue Date *</label>
                <input
                  id="issue_date"
                  v-model="form.issue_date"
                  type="date"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                <input
                  id="expiry_date"
                  v-model="form.expiry_date"
                  type="date"
                  :min="form.issue_date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
                <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
              </div>
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select
                  id="status"
                  v-model="form.status"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="active">Active</option>
                  <option value="pending">Pending</option>
                  <option value="draft">Draft</option>
                </select>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Certificate description..."
              ></textarea>
              <p class="text-xs text-gray-500 mt-1">Optional description that appears on the certificate</p>
            </div>

            <!-- Additional Settings -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-4">Additional Settings</h4>
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <input
                      id="is_public"
                      v-model="form.is_public"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="is_public" class="ml-2 text-sm text-gray-700">
                      Make certificate publicly accessible
                    </label>
                  </div>
                  <span class="text-xs text-gray-500">Users can share this certificate publicly</span>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <input
                      id="send_notification"
                      v-model="form.send_notification"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="send_notification" class="ml-2 text-sm text-gray-700">
                      Send notification to user
                    </label>
                  </div>
                  <span class="text-xs text-gray-500">User will receive email and app notification</span>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <input
                      id="generate_pdf"
                      v-model="form.generate_pdf"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="generate_pdf" class="ml-2 text-sm text-gray-700">
                      Generate PDF certificate
                    </label>
                  </div>
                  <span class="text-xs text-gray-500">Creates downloadable PDF version</span>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <input
                      id="generate_image"
                      v-model="form.generate_image"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="generate_image" class="ml-2 text-sm text-gray-700">
                      Generate shareable image
                    </label>
                  </div>
                  <span class="text-xs text-gray-500">Creates social media friendly image</span>
                </div>
              </div>
            </div>

            <!-- Certificate Metadata -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-4">Certificate Metadata (Optional)</h4>
              <div class="space-y-4">
                <div v-for="(metadata, index) in form.metadata" :key="index" class="flex items-center space-x-3">
                  <div class="flex-1">
                    <input
                      v-model="metadata.key"
                      type="text"
                      placeholder="Key"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                    />
                  </div>
                  <div class="flex-1">
                    <input
                      v-model="metadata.value"
                      type="text"
                      placeholder="Value"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                    />
                  </div>
                  <button
                    type="button"
                    @click="removeMetadata(index)"
                    class="px-2 py-2 text-red-600 hover:text-red-800"
                  >
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
                <button
                  type="button"
                  @click="addMetadata"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  Add Metadata Field
                </button>
              </div>
            </div>

            <!-- Preview Section -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-4">Certificate Preview</h4>
              <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6">
                <!-- Certificate Design -->
                <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden border-2 border-emerald-500">
                  <!-- Certificate Header -->
                  <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-center">
                    <h2 class="text-3xl font-bold text-white">Certificate of Completion</h2>
                    <p class="text-emerald-100 mt-2">This certifies that</p>
                  </div>

                  <!-- Certificate Body -->
                  <div class="p-8 text-center">
                    <!-- Student Name -->
                    <div class="mb-6">
                      <h3 class="text-4xl font-bold text-gray-800 mb-2">{{ user.name }}</h3>
                      <p class="text-gray-600">has successfully completed</p>
                    </div>

                    <!-- Course Title -->
                    <div class="mb-8">
                      <h4 class="text-2xl font-semibold text-emerald-700">{{ selectedCourse?.title || '[Course Title]' }}</h4>
                      <p class="text-gray-500 mt-2">{{ selectedCourse?.description || 'Course description goes here' }}</p>
                    </div>

                    <!-- Issuer Info -->
                    <div class="border-t border-gray-200 pt-6 mt-6">
                      <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                          <p class="text-sm text-gray-500">Issued by</p>
                          <p class="text-lg font-semibold text-gray-900">
                            {{ selectedIssuer?.name || 'Olilearn AI Learning Platform' }}
                          </p>
                        </div>
                        <div class="text-center">
                          <p class="text-sm text-gray-500">Issue Date</p>
                          <p class="text-lg font-semibold text-gray-900">{{ form.issue_date ? formatDate(form.issue_date) : 'Not set' }}</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                          <p class="text-sm text-gray-500">Certificate ID</p>
                          <p class="text-lg font-semibold text-gray-900">CERT-{{ new Date().getTime().toString().slice(-8) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Certificate Footer -->
                  <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center text-sm text-gray-500">
                      <div>
                        <p>Valid until: {{ form.expiry_date ? formatDate(form.expiry_date) : 'No expiry' }}</p>
                      </div>
                      <div>
                        <p>Status: <span :class="getStatusBadgeClass(form.status)">{{ form.status }}</span></p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-3 gap-4">
                  <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500">Progress</p>
                    <p class="text-lg font-bold text-emerald-600">{{ selectedCourse?.progress_percentage || 100 }}%</p>
                  </div>
                  <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500">Completion</p>
                    <p class="text-lg font-bold text-emerald-600">{{ selectedCourse?.completed_at ? formatDate(selectedCourse.completed_at) : 'N/A' }}</p>
                  </div>
                  <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500">Visibility</p>
                    <p class="text-lg font-bold text-emerald-600">{{ form.is_public ? 'Public' : 'Private' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <Link
                :href="route('admin.users.certificates', user.id)"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancel
              </Link>
              <button
                type="button"
                @click="saveAsDraft"
                :disabled="form.processing || !form.course_id"
                :class="[
                  'px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium',
                  form.processing || !form.course_id
                    ? 'text-gray-400 bg-gray-100 cursor-not-allowed'
                    : 'text-gray-700 bg-white hover:bg-gray-50'
                ]"
              >
                Save as Draft
              </button>
              <button
                type="submit"
                :disabled="form.processing || !form.course_id"
                :class="[
                  'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                  form.processing || !form.course_id
                    ? 'bg-blue-400 cursor-not-allowed'
                    : 'bg-blue-600 hover:bg-blue-700'
                ]"
              >
                <span v-if="form.processing">
                  <ArrowPathIcon class="h-4 w-4 inline animate-spin mr-2" />
                  Generating...
                </span>
                <span v-else>Generate Certificate</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Eligibility Check -->
        <div v-if="completedCourses.length > 0" class="mt-8 bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Course Eligibility Status</h3>
            <p class="text-sm text-gray-600">Detailed eligibility check for each completed course</p>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="course in completedCourses" :key="course.id"
                :class="[
                  'p-4 border rounded-lg',
                  course.can_generate && !course.has_certificate
                    ? 'border-green-200 bg-green-50'
                    : course.has_certificate
                    ? 'border-amber-200 bg-amber-50'
                    : 'border-red-200 bg-red-50'
                ]">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">{{ course.title }}</h4>
                    <div class="flex items-center space-x-4 mt-2">
                      <div class="flex items-center">
                        <CalendarDaysIcon class="h-4 w-4 text-gray-400 mr-1" />
                        <span class="text-xs text-gray-600">{{ formatDate(course.completed_at) }}</span>
                      </div>
                      <div class="flex items-center">
                        <ChartBarIcon class="h-4 w-4 text-gray-400 mr-1" />
                        <span class="text-xs text-gray-600">{{ course.progress_percentage }}% progress</span>
                      </div>
                    </div>
                  </div>
                  <div>
                    <div v-if="course.has_certificate" class="flex items-center">
                      <CheckCircleIcon class="h-5 w-5 text-amber-500 mr-2" />
                      <span class="text-sm font-medium text-amber-700">Certificate Exists</span>
                    </div>
                    <div v-else-if="course.can_generate" class="flex items-center">
                      <CheckCircleIcon class="h-5 w-5 text-green-500 mr-2" />
                      <span class="text-sm font-medium text-green-700">Eligible</span>
                    </div>
                    <div v-else class="flex items-center">
                      <XCircleIcon class="h-5 w-5 text-red-500 mr-2" />
                      <span class="text-sm font-medium text-red-700">Not Eligible</span>
                    </div>
                  </div>
                </div>

                <!-- Requirements Check -->
                <div v-if="!course.has_certificate" class="mt-3 pt-3 border-t border-gray-200">
                  <h5 class="text-xs font-medium text-gray-700 mb-2">Requirements Check:</h5>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="flex items-center">
                      <span class="flex-shrink-0 h-5 w-5 flex items-center justify-center rounded-full"
                        :class="course.progress_percentage >= 100 ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'">
                        <CheckIcon v-if="course.progress_percentage >= 100" class="h-3 w-3" />
                        <XMarkIcon v-else class="h-3 w-3" />
                      </span>
                      <span class="ml-2 text-xs" :class="course.progress_percentage >= 100 ? 'text-green-700' : 'text-red-700'">
                        100% Course Completion
                      </span>
                    </div>
                    <div class="flex items-center">
                      <span class="flex-shrink-0 h-5 w-5 flex items-center justify-center rounded-full"
                        :class="course.modules_completed === course.total_modules ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'">
                        <CheckIcon v-if="course.modules_completed === course.total_modules" class="h-3 w-3" />
                        <XMarkIcon v-else class="h-3 w-3" />
                      </span>
                      <span class="ml-2 text-xs" :class="course.modules_completed === course.total_modules ? 'text-green-700' : 'text-red-700'">
                        All Modules Completed ({{ course.modules_completed }}/{{ course.total_modules }})
                      </span>
                    </div>
                    <div class="flex items-center">
                      <span class="flex-shrink-0 h-5 w-5 flex items-center justify-center rounded-full"
                        :class="course.capstone_approved ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'">
                        <CheckIcon v-if="course.capstone_approved" class="h-3 w-3" />
                        <XMarkIcon v-else class="h-3 w-3" />
                      </span>
                      <span class="ml-2 text-xs" :class="course.capstone_approved ? 'text-green-700' : 'text-red-700'">
                        Capstone Project Approved
                      </span>
                    </div>
                    <div class="flex items-center">
                      <span class="flex-shrink-0 h-5 w-5 flex items-center justify-center rounded-full"
                        :class="!course.has_certificate ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'">
                        <CheckIcon v-if="!course.has_certificate" class="h-3 w-3" />
                        <XMarkIcon v-else class="h-3 w-3" />
                      </span>
                      <span class="ml-2 text-xs" :class="!course.has_certificate ? 'text-green-700' : 'text-red-700'">
                        No Existing Certificate
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Auto-generate Modal -->
    <Modal :show="showAutoGenerateModal" @close="showAutoGenerateModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Auto-generate Certificates
        </h3>
        <div class="space-y-4">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
              This will generate certificates for all eligible courses. The system will automatically:
            </p>
            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
              <li>Generate unique certificate numbers</li>
              <li>Create verification URLs</li>
              <li>Generate PDF and image files</li>
              <li>Send notifications to the user</li>
            </ul>
          </div>

          <div v-if="eligibleCourses.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-2">Courses to Generate:</label>
            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
              <div v-for="course in eligibleCourses" :key="course.id" class="flex items-center mb-3 last:mb-0">
                <CheckCircleIcon class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ course.title }}</p>
                  <p class="text-xs text-gray-500">Completed: {{ formatDate(course.completed_at) }}</p>
                </div>
              </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
              {{ eligibleCourses.length }} course(s) will be processed
            </p>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showAutoGenerateModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitAutoGenerate"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
            >
              Generate {{ eligibleCourses.length }} Certificate(s)
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Save as Draft Modal -->
    <Modal :show="showSaveDraftModal" @close="showSaveDraftModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Save Certificate as Draft
        </h3>
        <div class="space-y-4">
          <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <p class="text-sm text-amber-800">
              Saving as draft will create the certificate but mark it as "draft" status. You can:
            </p>
            <ul class="mt-2 text-sm text-amber-700 list-disc list-inside">
              <li>Generate files later when ready</li>
              <li>Send notifications later</li>
              <li>Edit details before finalizing</li>
              <li>Preview without making it active</li>
            </ul>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Draft Notes (Optional)</label>
            <textarea
              v-model="draftNotes"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Add notes about why this is being saved as draft..."
            ></textarea>
          </div>

          <div class="flex items-center">
            <input
              id="draft_generate_files"
              v-model="draftGenerateFiles"
              type="checkbox"
              class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="draft_generate_files" class="ml-2 text-sm text-gray-700">
              Generate files now (PDF & Image)
            </label>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showSaveDraftModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitSaveDraft"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700"
            >
              Save as Draft
            </button>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  BoltIcon,
  PlusIcon,
  TrashIcon,
  ArrowPathIcon,
  CalendarDaysIcon,
  ChartBarIcon,
  XCircleIcon,
  CheckIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  user: Object,
  completedCourses: Array,
  organizations: Array,
})

// Modals
const showAutoGenerateModal = ref(false)
const showSaveDraftModal = ref(false)

// Draft state
const draftNotes = ref('')
const draftGenerateFiles = ref(false)

// Filter eligible courses
const eligibleCourses = computed(() => {
  return props.completedCourses.filter(course =>
    course.can_generate && !course.has_certificate
  )
})

const selectedCourse = computed(() => {
  return props.completedCourses.find(course => course.id === form.course_id)
})

const selectedIssuer = computed(() => {
  if (!form.organization_id) return null
  return props.organizations.find(org => org.id == form.organization_id)
})

const form = useForm({
  user_id: props.user.id,
  course_id: null,
  organization_id: null,
  title: `Certificate of Completion`,
  description: `This certifies that ${props.user.name} has successfully completed the course.`,
  issue_date: new Date().toISOString().split('T')[0],
  expiry_date: new Date(new Date().setFullYear(new Date().getFullYear() + 2)).toISOString().split('T')[0],
  status: 'active',
  is_public: true,
  send_notification: true,
  generate_pdf: true,
  generate_image: true,
  metadata: [],
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const getStatusBadgeClass = (status) => {
  const classes = {
    active: 'text-green-700 bg-green-100 px-2 py-1 rounded text-xs',
    pending: 'text-amber-700 bg-amber-100 px-2 py-1 rounded text-xs',
    draft: 'text-gray-700 bg-gray-100 px-2 py-1 rounded text-xs',
  }
  return classes[status] || 'text-gray-700 bg-gray-100 px-2 py-1 rounded text-xs'
}

const addMetadata = () => {
  form.metadata.push({ key: '', value: '' })
}

const removeMetadata = (index) => {
  form.metadata.splice(index, 1)
}

const autoGenerateCertificate = () => {
  if (eligibleCourses.value.length === 0) {
    alert('No eligible courses found.')
    return
  }
  showAutoGenerateModal.value = true
}

const submitAutoGenerate = async () => {
  try {
    const response = await router.post(route('admin.certificates.batch-generate'), {
      user_id: props.user.id,
      course_ids: eligibleCourses.value.map(course => course.id),
    }, {
      preserveScroll: true,
    })

    showAutoGenerateModal.value = false
  } catch (error) {
    console.error('Failed to auto-generate certificates:', error)
    alert('Failed to generate certificates. Please try again.')
  }
}

const saveAsDraft = () => {
  showSaveDraftModal.value = true
}

const submitSaveDraft = () => {
  form.status = 'draft'
  form.send_notification = false
  form.generate_pdf = draftGenerateFiles.value
  form.generate_image = draftGenerateFiles.value

  // Add draft notes to metadata
  if (draftNotes.value) {
    form.metadata.push({ key: 'draft_notes', value: draftNotes.value })
  }

  submit()
}

const submit = () => {
  // Filter out empty metadata
  const filteredMetadata = form.metadata.filter(item =>
    item.key.trim() !== '' && item.value.trim() !== ''
  )

  form.metadata = filteredMetadata

  form.post(route('admin.certificates.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Success message will be shown on the certificates page
    }
  })
}

// Watch course selection to update title
watch(() => form.course_id, (newCourseId) => {
  const course = props.completedCourses.find(c => c.id === newCourseId)
  if (course) {
    form.title = `Certificate of Completion - ${course.title}`
    form.description = `This certifies that ${props.user.name} has successfully completed the course '${course.title}' on ${formatDate(course.completed_at)}.`
  }
})

// Auto-select first eligible course if only one exists
watch(() => props.completedCourses, (courses) => {
  const eligible = courses.filter(course =>
    course.can_generate && !course.has_certificate
  )

  if (eligible.length === 1 && !form.course_id) {
    form.course_id = eligible[0].id
  }
}, { immediate: true })
</script>
