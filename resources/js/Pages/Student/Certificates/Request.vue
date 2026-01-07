<!-- resources/js/Pages/Student/Certificates/Request.vue -->
<template>
  <StudentLayout>
    <Head title="Request Certificate" />

    <div class="py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Request Certificate</h1>
          <p class="mt-2 text-gray-600">
            Request certificates for completed courses
          </p>
        </div>

        <!-- Completed Courses List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Completed Courses</h2>
            <p class="text-sm text-gray-600 mt-1">
              Select courses you've completed to request certificates
            </p>
          </div>
          
          <div v-if="completedCourses.length > 0" class="divide-y divide-gray-200">
            <div
              v-for="course in completedCourses"
              :key="course.id"
              class="p-6 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900">{{ course.title }}</h3>
                  <div class="mt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                      {{ course.progress_percentage }}% Complete
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ course.modules_count }} Modules
                    </span>
                    <span v-if="course.capstone_project?.is_approved" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                      Capstone Approved
                    </span>
                  </div>
                </div>
                
                <div class="ml-4">
                  <div v-if="hasCertificate(course)" class="text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800 mb-2">
                      Certificate Issued
                    </span>
                    <Link
                      :href="route('student.certificates.show', getCertificateId(course))"
                      class="text-sm text-emerald-600 hover:text-emerald-800 font-medium"
                    >
                      View Certificate
                    </Link>
                  </div>
                  <button
                    v-else
                    @click="requestCertificate(course)"
                    :disabled="!isEligible(course) || requesting"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    Request Certificate
                  </button>
                </div>
              </div>

              <!-- Eligibility Status -->
              <div v-if="!hasCertificate(course)" class="mt-4 pt-4 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Eligibility Status</h4>
                <div class="space-y-2">
                  <div
                    v-for="requirement in getCourseRequirements(course)"
                    :key="requirement.text"
                    class="flex items-center text-sm"
                  >
                    <CheckCircleIcon
                      v-if="requirement.met"
                      class="h-4 w-4 text-emerald-500 mr-2"
                    />
                    <XCircleIcon
                      v-else
                      class="h-4 w-4 text-red-500 mr-2"
                    />
                    <span :class="requirement.met ? 'text-gray-600' : 'text-gray-400'">
                      {{ requirement.text }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="p-12 text-center">
            <AcademicCapIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Completed Courses</h3>
            <p class="text-gray-600 mb-6">
              Complete courses to request certificates
            </p>
            <Link
              :href="route('student.courses.index')"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:shadow-lg"
            >
              Browse Courses
            </Link>
          </div>
        </div>

        <!-- Certificate Information -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-2">Certificate Features</h4>
              <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Digital certificate with unique verification URL</span>
                </li>
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Shareable on LinkedIn and other platforms</span>
                </li>
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Printable PDF format available</span>
                </li>
              </ul>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-2">Requirements</h4>
              <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Complete all course modules</span>
                </li>
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Complete capstone project (if applicable)</span>
                </li>
                <li class="flex items-start">
                  <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Achieve minimum 70% overall score</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
  CheckCircleIcon,
  XCircleIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
  completedCourses: Array,
  certificates: Array,
})

const requesting = ref(false)

// Check if course has certificate
const hasCertificate = (course) => {
  return props.certificates.some(cert => cert.course_id === course.id)
}

// Get certificate ID for a course
const getCertificateId = (course) => {
  const cert = props.certificates.find(cert => cert.course_id === course.id)
  return cert?.id
}

// Check if course is eligible for certificate
const isEligible = (course) => {
  const requirements = getCourseRequirements(course)
  return requirements.every(req => req.met)
}

// Get course requirements
const getCourseRequirements = (course) => {
  const completedModules = course.modules?.filter(m => m.is_completed).length || 0
  const totalModules = course.modules?.length || 0
  
  return [
    {
      text: `Complete all modules (${completedModules}/${totalModules})`,
      met: completedModules === totalModules && totalModules > 0
    },
    {
      text: 'Complete capstone project',
      met: course.capstone_project?.is_approved || false
    },
    {
      text: `Achieve 70% or higher (${course.progress_percentage}%)`,
      met: course.progress_percentage >= 70
    },
    {
      text: 'Course marked as completed',
      met: course.status === 'completed'
    }
  ]
}

// Request certificate
const requestCertificate = (course) => {
  if (!isEligible(course)) {
    alert('This course is not eligible for a certificate yet. Please complete all requirements.')
    return
  }

  if (confirm(`Request certificate for "${course.title}"?`)) {
    requesting.value = true
    router.post(route('student.certificates.request', course.id), {}, {
      onSuccess: () => {
        requesting.value = false
      },
      onError: () => {
        requesting.value = false
      }
    })
  }
}
</script>