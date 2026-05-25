<!-- resources/js/Pages/Admin/Courses/MassEnrollment/Index.vue -->
<template>
  <AdminLayout>
    <Head title="Mass Enroll Students" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Mass Enroll Students</h1>
              <p class="mt-2 text-gray-600">
                Enroll multiple students in a course at once.
              </p>
            </div>
            <Link
              :href="route('admin.courses.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to Courses
            </Link>
          </div>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-4">
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
            <div class="flex-shrink-0">
                <CheckCircleIcon class="h-5 w-5 text-green-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                {{ $page.props.flash.success }}
                </h3>
                <!-- Update this part -->
                <div v-if="$page.props.flash.details" class="mt-2 text-sm text-green-700">
                <p>Enrolled: {{ $page.props.flash.details.enrolled }}</p>
                <p>Skipped: {{ $page.props.flash.details.skipped }}</p>
                <p>Total: {{ $page.props.flash.details.total }}</p>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div v-if="$page.props.flash.errors" class="mb-4">
          <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  Some issues occurred during enrollment
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="error in $page.props.flash.errors" :key="error">
                      {{ error }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Course Selection -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Course Selection Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">1. Select Course</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">
                      Course *
                    </label>
                    <select
                      id="course_id"
                      v-model="form.course_id"
                      @change="loadCourseDetails"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                      :disabled="loading"
                    >
                      <option value="">Select a course...</option>
                      <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course.id"
                      >
                        {{ course.title }} ({{ course.subject }} - {{ course.level }})
                      </option>
                    </select>
                    <p v-if="errors.course_id" class="mt-2 text-sm text-red-600">
                      {{ errors.course_id }}
                    </p>
                  </div>

                  <!-- Course Details -->
                  <div v-if="selectedCourse" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-start justify-between">
                      <div>
                        <h4 class="font-semibold text-gray-900">{{ selectedCourse.title }}</h4>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                          <div>
                            <span class="text-xs text-gray-500">Subject</span>
                            <p class="text-sm text-gray-900">{{ selectedCourse.subject }}</p>
                          </div>
                          <div>
                            <span class="text-xs text-gray-500">Level</span>
                            <p class="text-sm text-gray-900 capitalize">{{ selectedCourse.level }}</p>
                          </div>
                          <div>
                            <span class="text-xs text-gray-500">Duration</span>
                            <p class="text-sm text-gray-900">{{ selectedCourse.estimated_duration_hours }} hours</p>
                          </div>
                          <div>
                            <span class="text-xs text-gray-500">Enrollment</span>
                            <p class="text-sm text-gray-900">
                              {{ selectedCourse.current_enrollment }} /
                              {{ selectedCourse.enrollment_limit || 'Unlimited' }}
                            </p>
                          </div>
                        </div>
                      </div>
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="selectedCourse.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'"
                      >
                        {{ selectedCourse.status }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Student Selection Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-900">2. Select Students</h3>
                  <div class="flex space-x-2">
                    <button
                      @click="toggleSelectAll"
                      type="button"
                      class="text-sm text-blue-600 hover:text-blue-800"
                    >
                      {{ allSelected ? 'Deselect All' : 'Select All' }}
                    </button>
                    <button
                      @click="showImportModal = true"
                      type="button"
                      class="text-sm text-green-600 hover:text-green-800"
                    >
                      Import from CSV
                    </button>
                  </div>
                </div>
              </div>
              <div class="p-6">
                <!-- Search -->
                <div class="mb-4">
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search students by name or email..."
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    @input="searchStudents"
                  />
                </div>

                <!-- Student List -->
                <div class="space-y-3 max-h-96 overflow-y-auto">
                  <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                  >
                    <div class="flex items-center space-x-3">
                      <input
                        :id="`student-${student.id}`"
                        v-model="selectedStudentIds"
                        :value="student.id"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                      <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                          <span class="text-sm font-medium text-blue-800">
                            {{ student.name?.charAt(0).toUpperCase() || 'S' }}
                          </span>
                        </div>
                      </div>
                      <div>
                        <label :for="`student-${student.id}`" class="text-sm font-medium text-gray-900 cursor-pointer">
                          {{ student.name }}
                        </label>
                        <p class="text-xs text-gray-500">{{ student.email }}</p>
                        <div v-if="student.student_profile" class="mt-1">
                          <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            {{ student.student_profile.education_level || 'Not specified' }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div v-if="isEnrolled(student.id)" class="text-xs text-green-600">
                      <CheckCircleIcon class="h-4 w-4" />
                    </div>
                  </div>

                  <!-- Empty State -->
                  <div v-if="filteredStudents.length === 0" class="text-center py-8">
                    <UserGroupIcon class="h-12 w-12 mx-auto text-gray-400" />
                    <h4 class="mt-2 text-sm font-medium text-gray-900">No students found</h4>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ searchQuery ? 'Try a different search term' : 'No students available' }}
                    </p>
                  </div>
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                  <span>
                    {{ selectedStudentIds.length }} of {{ filteredStudents.length }} selected
                  </span>
                  <span v-if="selectedCourse && selectedCourse.enrollment_limit">
                    Capacity: {{ selectedCourse.current_enrollment }}/{{ selectedCourse.enrollment_limit }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Settings & Actions -->
          <div class="space-y-6">
            <!-- Notification Settings -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">3. Notification Settings</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="send_notifications"
                        v-model="form.send_notifications"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="send_notifications" class="font-medium text-gray-700">
                        Send enrollment notifications
                      </label>
                      <p class="text-gray-500">
                        Students will receive an email and in-app notification
                      </p>
                    </div>
                  </div>

                  <div v-if="form.send_notifications">
                    <label for="notification_message" class="block text-sm font-medium text-gray-700">
                      Custom Message (optional)
                    </label>
                    <textarea
                      id="notification_message"
                      v-model="form.notification_message"
                      rows="4"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      placeholder="Add a personalized message for the students..."
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500">
                      {{ form.notification_message?.length || 0 }}/500 characters
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">4. Confirm & Enroll</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <!-- Summary -->
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Enrollment Summary</h4>
                    <dl class="space-y-2">
                      <div class="flex justify-between">
                        <dt class="text-sm text-blue-700">Course</dt>
                        <dd class="text-sm font-medium text-blue-900">
                          {{ selectedCourse?.title || 'Not selected' }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-sm text-blue-700">Students Selected</dt>
                        <dd class="text-sm font-medium text-blue-900">
                          {{ selectedStudentIds.length }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-sm text-blue-700">Notifications</dt>
                        <dd class="text-sm font-medium text-blue-900">
                          {{ form.send_notifications ? 'Enabled' : 'Disabled' }}
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <!-- Enroll Button -->
                  <button
                    @click="submitEnrollment"
                    :disabled="!canSubmit || processing"
                    class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <template v-if="processing">
                      <ArrowPathIcon class="animate-spin h-4 w-4 mr-2" />
                      Processing...
                    </template>
                    <template v-else>
                      Enroll {{ selectedStudentIds.length }} Student(s)
                    </template>
                  </button>

                  <!-- Status Info -->
                  <div v-if="selectedCourse" class="text-xs text-gray-500">
                    <p class="flex items-center">
                      <InformationCircleIcon class="h-4 w-4 mr-1" />
                      Students already enrolled in this course will be skipped.
                    </p>
                    <p v-if="selectedCourse.enrollment_limit" class="mt-1">
                      Available spots: {{ Math.max(0, selectedCourse.enrollment_limit - selectedCourse.current_enrollment) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CSV Import Modal -->
    <Modal :show="showImportModal" @close="showImportModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Import Students from CSV</h3>

        <div class="mb-6">
          <p class="text-sm text-gray-600 mb-3">
            Upload a CSV file with student emails. The file should contain one email per line:
          </p>
          <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">
student1@example.com
student2@example.com
student3@example.com</pre>
          <p class="mt-3 text-xs text-gray-500">
            Only active students with valid emails will be imported.
          </p>
        </div>

        <div class="mb-4">
          <input
            ref="fileInput"
            type="file"
            accept=".csv,.txt"
            @change="handleFileUpload"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
        </div>

        <!-- Upload Progress -->
        <div v-if="uploading" class="mb-4">
          <div class="flex items-center justify-between text-sm text-gray-600">
            <span>Uploading and processing...</span>
            <ArrowPathIcon class="animate-spin h-4 w-4" />
          </div>
        </div>

        <!-- Upload Results -->
        <div v-if="uploadResults" class="mb-6">
          <div class="rounded-md bg-gray-50 p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Upload Results</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Total entries:</span>
                <span class="ml-2 font-medium">{{ uploadResults.total_processed }}</span>
              </div>
              <div>
                <span class="text-green-600">Valid students:</span>
                <span class="ml-2 font-medium">{{ uploadResults.valid_count }}</span>
              </div>
              <div>
                <span class="text-red-600">Invalid entries:</span>
                <span class="ml-2 font-medium">{{ uploadResults.invalid_count }}</span>
              </div>
            </div>

            <!-- Invalid entries list -->
            <div v-if="uploadResults.invalid_entries?.length" class="mt-3">
              <details class="text-sm">
                <summary class="text-red-600 cursor-pointer">View issues</summary>
                <ul class="mt-2 space-y-1 text-xs text-red-500">
                  <li v-for="(entry, index) in uploadResults.invalid_entries.slice(0, 5)" :key="index">
                    • {{ entry }}
                  </li>
                  <li v-if="uploadResults.invalid_entries.length > 5">
                    ... and {{ uploadResults.invalid_entries.length - 5 }} more
                  </li>
                </ul>
              </details>
            </div>
          </div>

          <!-- Valid students preview -->
          <div v-if="uploadResults.valid_students?.length" class="mt-4">
            <h5 class="text-sm font-medium text-gray-900 mb-2">Students to be added:</h5>
            <div class="max-h-40 overflow-y-auto space-y-2">
              <div
                v-for="student in uploadResults.valid_students"
                :key="student.id"
                class="flex items-center justify-between p-2 bg-white border border-gray-200 rounded"
              >
                <div>
                  <div class="text-sm font-medium">{{ student.name }}</div>
                  <div class="text-xs text-gray-500">{{ student.email }}</div>
                </div>
                <CheckCircleIcon class="h-4 w-4 text-green-500" />
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-3">
          <button
            @click="showImportModal = false"
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            v-if="uploadResults?.valid_students?.length"
            @click="useImportedStudents"
            :disabled="!selectedCourse"
            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50"
          >
            Add {{ uploadResults.valid_count }} Student(s)
          </button>
        </div>
      </div>
    </Modal>

    <!-- Processing Modal -->
    <Modal :show="processing" @close="processing = false">
      <div class="p-6 text-center">
        <ArrowPathIcon class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">Enrolling Students</h3>
        <p class="text-sm text-gray-600">
          Please wait while we process {{ selectedStudentIds.length }} enrollment(s)...
        </p>
        <div class="mt-4 bg-gray-200 rounded-full h-2">
          <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: `${progress}%` }"></div>
        </div>
        <p class="mt-2 text-xs text-gray-500">
          This may take a moment depending on the number of students.
        </p>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  UserGroupIcon,
  InformationCircleIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  courses: Array,
  students: Array,
  filters: Object,
  selected_course: Object,
})

// Refs
const form = ref({
  course_id: props.filters.course_id || '',
  student_ids: [],
  send_notifications: true,
  notification_message: '',
})

const searchQuery = ref('')
const selectedStudentIds = ref([])
const selectedCourse = ref(props.selected_course)
const loading = ref(false)
const processing = ref(false)
const progress = ref(0)
const showImportModal = ref(false)
const uploading = ref(false)
const uploadResults = ref(null)
const fileInput = ref(null)
const errors = ref({})

// Computed
const allSelected = computed(() => {
  return filteredStudents.value.length > 0 &&
         selectedStudentIds.value.length === filteredStudents.value.length
})

const canSubmit = computed(() => {
  return form.value.course_id && selectedStudentIds.value.length > 0
})

const filteredStudents = computed(() => {
  if (!searchQuery.value) {
    return props.students
  }

  const query = searchQuery.value.toLowerCase()
  return props.students.filter(student =>
    student.name.toLowerCase().includes(query) ||
    student.email.toLowerCase().includes(query)
  )
})

// Methods
const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedStudentIds.value = []
  } else {
    selectedStudentIds.value = filteredStudents.value.map(s => s.id)
  }
}

const loadCourseDetails = async () => {
  if (!form.value.course_id) {
    selectedCourse.value = null
    selectedStudentIds.value = []
    return
  }

  loading.value = true
  try {
    const response = await fetch(
      route('admin.courses.mass-enrollment.get-eligible-students', {
        course: form.value.course_id,
        search: searchQuery.value
      })
    )
    const data = await response.json()

    selectedCourse.value = props.courses.find(c => c.id == form.value.course_id)
    // Update selected students list
    selectedStudentIds.value = []
  } catch (error) {
    console.error('Failed to load course details:', error)
  } finally {
    loading.value = false
  }
}

const isEnrolled = (studentId) => {
  if (!selectedCourse.value) return false
  return selectedCourse.value.enrollments?.some(e => e.user_id === studentId)
}

const searchStudents = async () => {
  if (!form.value.course_id) return

  try {
    const response = await fetch(
      route('admin.courses.mass-enrollment.get-eligible-students', {
        course: form.value.course_id,
        search: searchQuery.value
      })
    )
    const data = await response.json()
    // Update students list with filtered results
    // Note: In a real implementation, you'd want to merge this with your existing students data
  } catch (error) {
    console.error('Failed to search students:', error)
  }
}

const submitEnrollment = () => {
  if (!canSubmit.value) return

  form.value.student_ids = selectedStudentIds.value

  if (selectedCourse.value?.enrollment_limit &&
      (selectedCourse.value.current_enrollment + form.value.student_ids.length) > selectedCourse.value.enrollment_limit) {
    if (!confirm(`This will exceed the course enrollment limit. Continue anyway?`)) {
      return
    }
  }

  processing.value = true
  progress.value = 0

  // Simulate progress
  const interval = setInterval(() => {
    progress.value = Math.min(progress.value + 10, 90)
  }, 500)

  router.post(route('admin.courses.mass-enrollment.store'), form.value, {
    onFinish: () => {
      clearInterval(interval)
      processing.value = false
      progress.value = 0
    },
    onSuccess: () => {
      // Reset form
      selectedStudentIds.value = []
      form.value.notification_message = ''
    }
  })
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file || !form.value.course_id) {
    alert('Please select a course first')
    return
  }

  uploading.value = true
  uploadResults.value = null

  const formData = new FormData()
  formData.append('course_id', form.value.course_id)
  formData.append('csv_file', file)

  try {
    const response = await fetch(route('admin.courses.mass-enrollment.upload-csv'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: formData,
    })

    const data = await response.json()
    uploadResults.value = data

  } catch (error) {
    console.error('Upload failed:', error)
    alert('Failed to upload file. Please try again.')
  } finally {
    uploading.value = false
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
}

const useImportedStudents = () => {
  if (uploadResults.value?.valid_students) {
    const importedIds = uploadResults.value.valid_students.map(s => s.id)
    const newIds = importedIds.filter(id => !selectedStudentIds.value.includes(id))
    selectedStudentIds.value = [...selectedStudentIds.value, ...newIds]
    showImportModal.value = false
    uploadResults.value = null
  }
}

// Initialize
if (props.filters.course_id) {
  loadCourseDetails()
}
</script>
