<template>
  <Head title="Join Olilearn" />

  <!-- Background Elements -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-emerald-100 rounded-full opacity-30 animate-float"></div>
    <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-teal-100 rounded-full opacity-40 animate-float-delay"></div>
    <div class="absolute top-1/3 right-1/3 w-32 h-32 bg-cyan-100 rounded-full opacity-50 animate-float" style="animation-delay: 2s;"></div>
  </div>

  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                <img
                src="/logo-olilearn.PNG"
                alt="OliLearn"
                class="h-16 w-auto object-contain"
                />
            </div>
            </div>

      <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
        Join Olilearn Platform
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Already have an account?
        <Link :href="route('login')" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
          Sign in here
        </Link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 py-8 px-6 sm:px-10">
        <!-- Step Indicator -->
        <div class="mb-8">
          <div class="flex items-center justify-center space-x-8">
            <div
              v-for="(step, index) in steps"
              :key="step.name"
              class="flex items-center"
            >
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                :class="getStepClasses(step)"
              >
                {{ index + 1 }}
              </div>
              <span
                class="ml-3 text-sm font-medium hidden sm:block"
                :class="getStepTextClasses(step)"
              >
                {{ step.name }}
              </span>
              <div
                v-if="index < steps.length - 1"
                class="ml-6 w-12 h-0.5 bg-gray-300 hidden sm:block"
              ></div>
            </div>
          </div>
        </div>

        <!-- Step 1: Role Selection -->
        <div v-if="currentStep === 1" class="space-y-6">
          <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">
              Choose Your Role
            </h3>
            <p class="text-gray-600">
              Select how you want to use Olilearn platform
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Student Card -->
            <div
              class="border-2 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-lg"
              :class="form.role === 'student' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300'"
              @click="selectRole('student')"
            >
              <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                  <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                  </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Learner</h4>
                <p class="text-sm text-gray-600 mb-4">
                  Learn with AI-powered tutoring and personalized courses
                </p>
                <ul class="text-xs text-gray-500 space-y-1 text-left">
                  <li>• Personalized learning paths</li>
                  <li>• AI-powered explanations</li>
                  <li>• Progress tracking</li>
                  <li>• Exam preparation</li>
                </ul>
              </div>
            </div>



            <!-- Organization Card -->
            <div
              class="border-2 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-lg"
              :class="form.role === 'organization' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300'"
              @click="selectRole('organization')"
            >
              <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Organization</h4>
                <p class="text-sm text-gray-600 mb-4">
                  Manage tutors and students with institutional tools
                </p>
                <ul class="text-xs text-gray-500 space-y-1 text-left">
                  <li>• Multi-user management</li>
                  <li>• Advanced analytics</li>
                  <li>• Branded portal</li>
                  <li>• Team collaboration</li>
                </ul>
              </div>
            </div>
          </div>

          <div>
            <button
              @click="nextStep"
              :disabled="!form.role"
              class="w-full bg-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              Continue to Account Setup
              <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Step 2: Basic Information (Common for all roles) -->
        <form v-if="currentStep === 2" @submit.prevent="nextStep" class="space-y-6">
  <div class="text-center mb-6">
    <h3 class="text-xl font-bold text-gray-900">
      Create Your Account
    </h3>
    <p class="text-gray-600">
      {{ roleDescription }}
    </p>
  </div>

  <!-- Social Login Buttons - Only show for Student role -->
  <div v-if="form.role === 'student' && availableProviders.length > 0" class="space-y-4">
    <div class="text-center">
      <p class="text-sm text-gray-600 mb-4">Quick sign up with:</p>
    </div>
    <div class="grid grid-cols-2 gap-3">
      <!-- Google Login -->
      <a
        v-if="availableProviders.includes('google')"
        :href="route('social.redirect', { provider: 'google' })"
        class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors hover:shadow-md"
      >
        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Google
      </a>

      <!-- GitHub Login -->
      <a
        v-if="availableProviders.includes('github')"
        :href="route('social.redirect', { provider: 'github' })"
        class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors hover:shadow-md"
      >
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
        </svg>
        GitHub
      </a>
    </div>

    <!-- Divider - Only show if Student role is selected -->
    <div class="relative">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-gray-300"></div>
      </div>
      <div class="relative flex justify-center text-sm">
        <span class="px-2 bg-white text-gray-500">Or continue with email</span>
      </div>
    </div>
  </div>

  <!-- Show different message for Tutor and Organization -->
  <div v-else-if="form.role !== 'student'" class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
    <p class="text-sm text-blue-700">
      <strong>Professional Account:</strong> Kindly use a professional email for {{ form.role }} accounts to ensure proper verification and security.
    </p>
  </div>

  <!-- Rest of your form fields remain the same -->
  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
        Full Name
      </label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          autocomplete="name"
          class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
          :class="{ 'border-red-300': errors.name }"
          :placeholder="form.role === 'organization' ? 'Organization Name' : 'Enter your full name'"
        />
      </div>
      <p v-if="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ errors.name }}
      </p>
    </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                  </svg>
                </div>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  autocomplete="email"
                  class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  :class="{ 'border-red-300': errors.email }"
                  placeholder="Enter your email"
                />
              </div>
              <p v-if="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ errors.email }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  autocomplete="new-password"
                  class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  :class="{ 'border-red-300': errors.password }"
                  placeholder="Create a password"
                />
              </div>
              <p v-if="errors.password" class="mt-2 text-sm text-red-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ errors.password }}
              </p>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Confirm Password
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  autocomplete="new-password"
                  class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  placeholder="Confirm your password"
                />
              </div>
            </div>
          </div>

          <!-- Additional fields for organizations -->
          <div v-if="form.role === 'organization'" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                Website (Optional)
              </label>
              <input
                id="website"
                v-model="form.website"
                type="url"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="https://example.com"
              />
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                Phone Number
              </label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="+1 (555) 123-4567"
              />
            </div>
          </div>

          <div v-if="hasTermsAndPrivacyPolicyFeature" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
            <div class="flex items-start">
              <input
                id="terms"
                v-model="form.terms"
                type="checkbox"
                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1"
              />
              <label for="terms" class="ml-3 text-sm text-gray-700">
                I agree to the
                <a :href="route('terms.show')" class="text-emerald-600 hover:text-emerald-500 font-medium">Terms of Service</a>
                and
                <a :href="route('policy.show')" class="text-emerald-600 hover:text-emerald-500 font-medium">Privacy Policy</a>
              </label>
            </div>
            <p v-if="errors.terms" class="mt-2 text-sm text-red-600 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ errors.terms }}
            </p>
          </div>

          <div class="flex space-x-4">
            <button
              type="button"
              @click="previousStep"
              class="flex-1 py-3 px-4 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
            >
              Back
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 bg-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              Continue to {{ nextStepButtonText }}
              <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </button>
          </div>
        </form>

        <!-- Step 3: Role-Specific Profile -->
        <!-- Student Profile Form -->
        <form v-if="currentStep === 3 && form.role === 'student'" @submit.prevent="register" class="space-y-6">
          <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">
              Your Learning Profile
            </h3>
            <p class="text-gray-600">
              Help us personalize your learning experience
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="current_level" class="block text-sm font-medium text-gray-700 mb-2">
                Current Level
              </label>
              <select
                id="current_level"
                v-model="form.current_level"
                class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              >
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
              </select>
            </div>

            <div>
              <label for="target_level" class="block text-sm font-medium text-gray-700 mb-2">
                Target Level
              </label>
              <select
                id="target_level"
                v-model="form.target_level"
                class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              >
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
              </select>
            </div>
          </div>

          <div>
            <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-2">
              Exam Board (Optional)
            </label>
            <select
              id="exam_board_id"
              v-model="form.exam_board_id"
              class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
            >
              <option value="">Select an exam board</option>
              <option
                v-for="board in examBoards"
                :key="board.id"
                :value="board.id"
              >
                {{ board.name }}
              </option>
            </select>
            <p class="mt-2 text-sm text-gray-500">
              Choose if you're preparing for a specific examination
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="weekly_study_hours" class="block text-sm font-medium text-gray-700 mb-2">
                Weekly Study Hours
              </label>
              <select
                id="weekly_study_hours"
                v-model="form.weekly_study_hours"
                class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              >
                <option value="2">2-4 hours</option>
                <option value="5">5-7 hours</option>
                <option value="10">10+ hours</option>
              </select>
            </div>

            <div>
              <label for="target_completion_date" class="block text-sm font-medium text-gray-700 mb-2">
                Target Completion Date
              </label>
              <input
                id="target_completion_date"
                v-model="form.target_completion_date"
                type="date"
                :min="minDate"
                class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              />
            </div>
          </div>

          <!-- Learning Goals & Preferences for Students -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-4">
              What are your main learning goals?
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label
                v-for="goal in learningGoals"
                :key="goal.value"
                class="flex items-center p-4 border border-gray-300 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors"
                :class="{ 'border-emerald-500 bg-emerald-50': form.learning_goals.includes(goal.value) }"
              >
                <input
                  type="checkbox"
                  :value="goal.value"
                  v-model="form.learning_goals"
                  class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                />
                <span class="ml-3 text-sm text-gray-900 font-medium">
                  {{ goal.label }}
                </span>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-4">
              How do you prefer to learn?
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <label
                v-for="preference in learningPreferences"
                :key="preference.value"
                class="flex items-start p-4 border border-gray-300 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors"
                :class="{ 'border-emerald-500 bg-emerald-50': form.learning_preferences.includes(preference.value) }"
              >
                <input
                  type="checkbox"
                  :value="preference.value"
                  v-model="form.learning_preferences"
                  class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1"
                />
                <div class="ml-3">
                  <span class="text-sm font-medium text-gray-900">
                    {{ preference.label }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ preference.description }}
                  </p>
                </div>
              </label>
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              type="button"
              @click="previousStep"
              class="flex-1 py-3 px-4 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
            >
              Back
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 bg-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="processing">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating Account...
              </span>
              <span v-else>
                Create Student Account
              </span>
            </button>
          </div>
        </form>

        <!-- Tutor Profile Form -->
        <form v-if="currentStep === 3 && form.role === 'tutor'" @submit.prevent="register" class="space-y-6">
          <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">
              Tutor Profile Setup
            </h3>
            <p class="text-gray-600">
              Tell us about your teaching background and expertise
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                Highest Qualification
              </label>
              <input
                id="qualification"
                v-model="form.qualification"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="e.g., Masters in Mathematics"
              />
            </div>

            <div>
              <label for="years_of_experience" class="block text-sm font-medium text-gray-700 mb-2">
                Years of Experience
              </label>
              <select
                id="years_of_experience"
                v-model="form.years_of_experience"
                class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              >
                <option value="0">Less than 1 year</option>
                <option value="1">1-2 years</option>
                <option value="3">3-5 years</option>
                <option value="5">5+ years</option>
              </select>
            </div>
          </div>

          <div>
            <label for="specialties" class="block text-sm font-medium text-gray-700 mb-2">
              Teaching Specialties
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <label
                v-for="specialty in tutorSpecialties"
                :key="specialty.value"
                class="flex items-center p-3 border border-gray-300 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors"
                :class="{ 'border-blue-500 bg-blue-50': form.specialties.includes(specialty.value) }"
              >
                <input
                  type="checkbox"
                  :value="specialty.value"
                  v-model="form.specialties"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-900">
                  {{ specialty.label }}
                </span>
              </label>
            </div>
          </div>

          <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
              Teaching Bio
            </label>
            <textarea
              id="bio"
              v-model="form.bio"
              rows="4"
              class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              placeholder="Tell us about your teaching philosophy and experience..."
            ></textarea>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                Hourly Rate (USD)
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500">$</span>
                </div>
                <input
                  id="hourly_rate"
                  v-model="form.hourly_rate"
                  type="number"
                  min="0"
                  step="5"
                  class="form-input block w-full pl-8 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  placeholder="0.00"
                />
              </div>
            </div>

            <div>
              <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                Timezone
              </label>
              <select
                id="timezone"
                v-model="form.timezone"
                class="mt-1 block w-full pl-3 pr-10 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              >
                <option value="">Select your timezone</option>
                <option value="EST">Eastern Time (EST)</option>
                <option value="CST">Central Time (CST)</option>
                <option value="MST">Mountain Time (MST)</option>
                <option value="PST">Pacific Time (PST)</option>
                <option value="GMT">Greenwich Mean Time (GMT)</option>
              </select>
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              type="button"
              @click="previousStep"
              class="flex-1 py-3 px-4 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
            >
              Back
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="processing">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating Account...
              </span>
              <span v-else>
                Create Tutor Account
              </span>
            </button>
          </div>
        </form>

        <!-- Organization Profile Form -->
        <form v-if="currentStep === 3 && form.role === 'organization'" @submit.prevent="register" class="space-y-6">
          <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">
              Organization Details
            </h3>
            <p class="text-gray-600">
              Tell us about your organization
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Organization Description
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="Brief description of your organization..."
              ></textarea>
            </div>

            <div>
              <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-2">
                Founded Year
              </label>
              <input
                id="founded_year"
                v-model="form.founded_year"
                type="number"
                :min="1900"
                :max="new Date().getFullYear()"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="2020"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                Address
              </label>
              <input
                id="address"
                v-model="form.address"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="Street address"
              />
            </div>

            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                City
              </label>
              <input
                id="city"
                v-model="form.city"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="City"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div>
              <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                State/Province
              </label>
              <input
                id="state"
                v-model="form.state"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="State"
              />
            </div>

            <div>
              <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                Country
              </label>
              <input
                id="country"
                v-model="form.country"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="Country"
              />
            </div>

            <div>
              <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                Postal Code
              </label>
              <input
                id="postal_code"
                v-model="form.postal_code"
                type="text"
                class="form-input block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="ZIP/Postal code"
              />
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              type="button"
              @click="previousStep"
              class="flex-1 py-3 px-4 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
            >
              Back
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 bg-purple-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="processing">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating Account...
              </span>
              <span v-else>
                Create Organization Account
              </span>
            </button>
          </div>
        </form>

        <!-- Step 4: Success -->
        <div v-if="currentStep === 4" class="text-center space-y-6">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100">
            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900">
              Welcome to Olilearn!
            </h3>
            <p class="mt-2 text-gray-600">
              Your {{ form.role }} account has been created successfully.
            </p>
          </div>
          <div>
            <Link
              :href="getDashboardRoute"
              class="w-full bg-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors inline-flex items-center justify-center"
            >
              Go to Dashboard
              <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  errors: Object,
  hasTermsAndPrivacyPolicyFeature: Boolean,
  examBoards: Array,
  availableProviders: {
    type: Array,
    default: () => ['google', 'github'] // Default providers
  }
})

const currentStep = ref(1)
const processing = ref(false)

const steps = computed(() => {
  const baseSteps = [
    { name: 'Role', number: 1 },
    { name: 'Account', number: 2 },
    { name: 'Profile', number: 3 },
  ]
  return baseSteps
})

const form = useForm({
  // Step 1
  role: '', // student, tutor, organization

  // Step 2 (Common)
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
  website: '',
  phone: '',

  // Step 3 - Student specific
  current_level: 'beginner',
  target_level: 'intermediate',
  exam_board_id: '',
  weekly_study_hours: '5',
  target_completion_date: '',
  learning_goals: [],
  learning_preferences: [],

  // Step 3 - Tutor specific
  qualification: '',
  years_of_experience: '0',
  specialties: [],
  bio: '',
  hourly_rate: '',
  timezone: '',

  // Step 3 - Organization specific
  description: '',
  founded_year: '',
  address: '',
  city: '',
  state: '',
  country: '',
  postal_code: '',
})

const roleDescription = computed(() => {
  switch (form.role) {
    case 'student':
      return 'Create your student account to start learning'
    case 'tutor':
      return 'Create your tutor account to start teaching'
    case 'organization':
      return 'Create your organization account to manage tutors and students'
    default:
      return 'Create your account'
  }
})

const nextStepButtonText = computed(() => {
  switch (form.role) {
    case 'student':
      return 'Learning Profile'
    case 'tutor':
      return 'Tutor Profile'
    case 'organization':
      return 'Organization Details'
    default:
      return 'Profile Setup'
  }
})

const getDashboardRoute = computed(() => {
  switch (form.role) {
    case 'student':
      return route('student.dashboard')
    case 'tutor':
      return route('tutor.dashboard')
    case 'organization':
      return route('organization.dashboard')
    default:
      return route('dashboard')
  }
})

const learningGoals = [
  { value: 'exam_preparation', label: 'Exam Preparation' },
  { value: 'career_advancement', label: 'Career Advancement' },
  { value: 'skill_development', label: 'Skill Development' },
  { value: 'academic_studies', label: 'Academic Studies' },
  { value: 'personal_interest', label: 'Personal Interest' },
  { value: 'certification', label: 'Get Certified' },
]

const learningPreferences = [
  {
    value: 'visual',
    label: 'Visual',
    description: 'Diagrams and visual aids'
  },
  {
    value: 'interactive',
    label: 'Interactive',
    description: 'Hands-on exercises'
  },
  {
    value: 'reading',
    label: 'Reading',
    description: 'Text materials'
  },
  {
    value: 'video',
    label: 'Video',
    description: 'Video lectures'
  },
  {
    value: 'audio',
    label: 'Audio',
    description: 'Podcasts and audio'
  },
  {
    value: 'social',
    label: 'Social',
    description: 'Group learning'
  },
]

const tutorSpecialties = [
  { value: 'mathematics', label: 'Mathematics' },
  { value: 'science', label: 'Science' },
  { value: 'programming', label: 'Programming' },
  { value: 'languages', label: 'Languages' },
  { value: 'business', label: 'Business' },
  { value: 'arts', label: 'Arts' },
  { value: 'test_prep', label: 'Test Prep' },
  { value: 'music', label: 'Music' },
  { value: 'sports', label: 'Sports' },
]

const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const getStepClasses = (step) => {
  if (currentStep.value > step.number) {
    return 'bg-emerald-600 text-white shadow-md'
  } else if (currentStep.value === step.number) {
    return 'bg-emerald-600 text-white shadow-md ring-2 ring-emerald-200'
  } else {
    return 'bg-gray-200 text-gray-500'
  }
}

const getStepTextClasses = (step) => {
  if (currentStep.value >= step.number) {
    return 'text-gray-900'
  } else {
    return 'text-gray-500'
  }
}

const selectRole = (role) => {
  form.role = role
}

const validateStep1 = () => {
  const errors = {}

  if (!form.role) {
    errors.role = 'Please select a role'
  }

  return errors
}

const validateStep2 = () => {
  const errors = {}

  if (!form.name) errors.name = 'Name is required'
  if (!form.email) errors.email = 'Email is required'
  if (!form.password) errors.password = 'Password is required'
  if (form.password !== form.password_confirmation) {
    errors.password = 'Passwords do not match'
  }
  if (props.hasTermsAndPrivacyPolicyFeature && !form.terms) {
    errors.terms = 'You must accept the terms and conditions'
  }

  return errors
}

const nextStep = () => {
  if (currentStep.value === 1) {
    const errors = validateStep1()
    if (Object.keys(errors).length > 0) {
      Object.keys(errors).forEach(key => {
        if (props.errors) props.errors[key] = errors[key]
      })
      return
    }
  } else if (currentStep.value === 2) {
    const errors = validateStep2()
    if (Object.keys(errors).length > 0) {
      Object.keys(errors).forEach(key => {
        if (props.errors) props.errors[key] = errors[key]
      })
      return
    }
  }

  if (currentStep.value < 3) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const register = () => {
  processing.value = true

  // Set default values if needed
  if (form.role === 'student' && !form.target_completion_date) {
    const sixMonthsFromNow = new Date()
    sixMonthsFromNow.setMonth(sixMonthsFromNow.getMonth() + 6)
    form.target_completion_date = sixMonthsFromNow.toISOString().split('T')[0]
  }

  form.post(route('register'), {
    onFinish: () => {
      processing.value = false
    },
    onSuccess: () => {
      currentStep.value = 4
    },
  })
}

onMounted(() => {
  if (form.role === 'student' && !form.target_completion_date) {
    const sixMonthsFromNow = new Date()
    sixMonthsFromNow.setMonth(sixMonthsFromNow.getMonth() + 6)
    form.target_completion_date = sixMonthsFromNow.toISOString().split('T')[0]
  }
})
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-delay {
  animation: float 7s ease-in-out infinite;
  animation-delay: 1s;
}

.form-input:focus {
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}
</style>
