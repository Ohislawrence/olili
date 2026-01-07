<!-- resources/js/Pages/Student/Catalog/Show.vue -->
<template>
  <StudentLayout>
    <Head :title="course?.title || 'Course Preview'" />

    <div class="py-6" v-if="course">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
          <button
            @click="$inertia.visit(route('student.catalog.browse'))"
            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Catalog
          </button>
        </div>

        <!-- Course Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
          <div class="md:flex">
            <!-- Course Info -->
            <div class="md:w-full p-6 md:p-8">
              <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="mb-6">
                  <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span :class="getLevelBadgeClass(course.level)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                      {{ course.level }}
                    </span>
                    <span v-if="course.exam_board" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                      {{ course.exam_board.name }}
                    </span>
                    <span v-if="course.status !== 'active'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                      {{ course.status }}
                    </span>
                  </div>

                  <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ course.title }}</h1>

                  <p class="text-lg text-gray-700 mb-4">{{ course.subject }}</p>

                  <div class="flex items-center text-gray-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Created by {{ course.creator?.name || 'Admin Instructor' }}</span>
                  </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                  <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ course.estimated_duration_hours || 0 }}</div>
                    <div class="text-sm text-gray-600">Hours</div>
                  </div>
                  <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ module_count || 0 }}</div>
                    <div class="text-sm text-gray-600">Modules</div>
                  </div>
                  <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ topic_count || 0 }}</div>
                    <div class="text-sm text-gray-600">Topics</div>
                  </div>
                  <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ enrollment_stats?.total || 0 }}</div>
                    <div class="text-sm text-gray-600">Enrolled</div>
                  </div>
                </div>

                <!-- Enrollment Status -->
                <div class="mt-auto">
                  <div v-if="is_enrolled" class="mb-4">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                      <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                          <h3 class="font-medium text-emerald-800">You are enrolled in this course</h3>
                          <p class="text-sm text-emerald-700">
                            Progress: {{ enrolled_course?.progress_percentage || 0 }}% complete
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="space-y-3">
                    <div v-if="is_enrolled && enrolled_course">
                      <Link
                        :href="route('student.courses.learn', { course: course.id })"
                        class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Continue Learning
                      </Link>
                    </div>
                    <div v-else>
                      <div v-if="can_enroll" class="space-y-3">
                        <button
                          @click="enrollCourse"
                          :disabled="isEnrolling"
                          class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          <svg v-if="!isEnrolling" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                          </svg>
                          <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          {{ isEnrolling ? 'Enrolling...' : 'Enroll in this Course' }}
                        </button>
                        <div class="text-center text-sm text-gray-600">
                          Free enrollment • Start learning immediately
                        </div>
                      </div>
                      <div v-else class="text-center">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                          <p class="text-red-800 font-medium">
                            {{ enrollment_stats?.total >= enrollment_stats?.limit ? 'Course is full' : 'You cannot enroll in this course' }}
                          </p>
                          <p v-if="enrollment_stats?.total >= enrollment_stats?.limit" class="text-sm text-red-700 mt-1">
                            All {{ enrollment_stats?.limit }} spots have been filled
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="text-center text-sm text-gray-500">
                      <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Self-paced • Lifetime access
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Course Details -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Description -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Course Description</h2>
              <div class="prose max-w-none">
                <p class="text-gray-700 whitespace-pre-wrap">{{ course.description || 'No description available.' }}</p>
              </div>
            </div>

            <!-- Learning Objectives -->
            <div v-if="course.learning_objectives && course.learning_objectives.length" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">What You'll Learn</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="(objective, index) in course.learning_objectives"
                  :key="index"
                  class="flex items-start"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span class="text-gray-700">{{ objective }}</span>
                </div>
              </div>
            </div>

            <!-- Course Content -->
            <div v-if="course.modules && course.modules.length" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Course Content</h2>
                <div class="text-sm text-gray-600">
                  {{ module_count || 0 }} modules • {{ topic_count || 0 }} topics
                </div>
              </div>

              <div class="space-y-4">
                <div
                  v-for="module in course.modules"
                  :key="module.id"
                  class="border border-gray-200 rounded-lg overflow-hidden"
                >
                  <!-- Module Header -->
                  <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                      <h3 class="font-medium text-gray-900">{{ module.title }}</h3>
                      <div class="text-sm text-gray-500">
                        {{ module.topics?.length || 0 }} topics • {{ module.estimated_duration_minutes || 0 }} min
                      </div>
                    </div>
                    <p v-if="module.description" class="mt-1 text-sm text-gray-600">{{ module.description }}</p>
                  </div>

                  <!-- Topics List -->
                  <div v-if="module.topics && module.topics.length" class="divide-y divide-gray-200">
                    <div
                      v-for="topic in module.topics"
                      :key="topic.id"
                      class="px-5 py-3"
                    >
                      <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 mt-1">
                          <span class="text-gray-600 text-sm font-medium">{{ topic.order || '' }}</span>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-sm font-medium text-gray-900">{{ topic.title }}</h4>
                          <p v-if="topic.description" class="mt-1 text-sm text-gray-600">{{ topic.description }}</p>
                          <div class="mt-2 flex items-center text-xs text-gray-500">
                            <div class="flex items-center mr-4">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              {{ topic.estimated_duration_minutes || 0 }} min
                            </div>
                            <div v-if="topic.has_quiz" class="flex items-center mr-4">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                              </svg>
                              Quiz
                            </div>
                            <div v-if="topic.has_project" class="flex items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                              </svg>
                              Project
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="px-5 py-3 text-center text-sm text-gray-500">
                    No topics in this module
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Course Content</h2>
              <p class="text-gray-600">Course content is being prepared. Please check back later.</p>
            </div>
          </div>

          <!-- Right Column: Sidebar -->
          <div class="space-y-6">
            <!-- Prerequisites -->
            <div v-if="course.prerequisites && course.prerequisites.length" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
              <h3 class="font-medium text-gray-900 mb-3">Prerequisites</h3>
              <div class="space-y-2">
                <div
                  v-for="(prereq, index) in course.prerequisites"
                  :key="index"
                  class="flex items-start"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  <span class="text-sm text-gray-700">{{ prereq }}</span>
                </div>
              </div>
            </div>

            <!-- Enrollment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
              <h3 class="font-medium text-gray-900 mb-3">Enrollment Information</h3>
              <div class="space-y-3">
                <div class="flex items-center text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Self-paced learning</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  <span>Certificate of completion</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>AI-powered tutor</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <span>
                    {{ enrollment_stats?.total || 0 }} enrolled
                    <span v-if="enrollment_stats?.limit"> of {{ enrollment_stats?.limit }}</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Instructor -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
              <h3 class="font-medium text-gray-900 mb-3">Instructor</h3>
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                  <span class="text-emerald-800 font-medium">
                    {{ course.creator?.name?.charAt(0).toUpperCase() || 'A' }}
                  </span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ course.creator?.name || 'Admin Instructor' }}</div>
                  <div class="text-sm text-gray-600">Course Creator</div>
                </div>
              </div>
              <p v-if="course.creator?.bio" class="mt-3 text-sm text-gray-700">
                {{ course.creator.bio }}
              </p>
            </div>

            <!-- Share Course -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
              <h3 class="font-medium text-gray-900 mb-3">Share this course</h3>
              <div class="flex space-x-2">
                <button
                  @click="shareCourse('facebook')"
                  class="flex-1 inline-flex justify-center items-center p-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  @click="shareCourse('twitter')"
                  class="flex-1 inline-flex justify-center items-center p-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                  </svg>
                </button>
                <button
                  @click="shareCourse('linkedin')"
                  class="flex-1 inline-flex justify-center items-center p-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  @click="copyCourseLink"
                  class="flex-1 inline-flex justify-center items-center p-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                  title="Copy link"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const page = usePage()
const isEnrolling = ref(false)

const props = defineProps({
  course: {
    type: Object,
    default: () => ({})
  },
  is_enrolled: {
    type: Boolean,
    default: false
  },
  enrolled_course: {
    type: Object,
    default: null
  },
  can_enroll: {
    type: Boolean,
    default: false
  },
  module_count: {
    type: Number,
    default: 0
  },
  topic_count: {
    type: Number,
    default: 0
  },
  enrollment_stats: {
    type: Object,
    default: () => ({
      total: 0,
      limit: null,
      available: null
    })
  },
})

// Methods
const getLevelBadgeClass = (level) => {
  const classes = {
    beginner: 'bg-green-100 text-green-800',
    intermediate: 'bg-yellow-100 text-yellow-800',
    advanced: 'bg-orange-100 text-orange-800',
    expert: 'bg-red-100 text-red-800',
  }
  return classes[level] || 'bg-gray-100 text-gray-800'
}

const enrollCourse = async () => {
  if (isEnrolling.value) return

  isEnrolling.value = true

  try {
    await router.post(route('student.courses.enroll', props.course.id), {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        // Redirect to the course show page after enrollment
        router.visit(route('student.courses.show', props.course.id))
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to enroll in the course')
      },
      onFinish: () => {
        isEnrolling.value = false
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
    isEnrolling.value = false
  }
}

const shareCourse = (platform) => {
  const url = window.location.href
  const title = props.course?.title || 'Course Preview'
  const text = `Check out this course: ${title}`

  const shareUrls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
    twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`,
    linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
  }

  if (shareUrls[platform]) {
    window.open(shareUrls[platform], '_blank', 'width=600,height=400')
  }
}

const copyCourseLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    alert('Course link copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}
</script>

<style scoped>
.prose {
  line-height: 1.6;
}

.prose p {
  margin-bottom: 1em;
}
</style>
