<!-- resources/js/Pages/Admin/Users/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`User Details - ${user.name}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
              <p class="mt-2 text-gray-600">
                {{ user.email }}
                <span
                  class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getRoleBadgeClass(user.roles[0]?.name)"
                >
                  {{ user.roles[0]?.name }}
                </span>
              </p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.users.edit', user.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Edit User
              </Link>
              <Link
                :href="route('admin.users.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to Users
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Courses"
            :value="stats.total_courses"
            icon="academic-cap"
            color="blue"
          />
          <StatsCard
            title="Completed Courses"
            :value="stats.completed_courses"
            icon="check-circle"
            color="green"
          />
          <StatsCard
            title="Quiz Attempts"
            :value="stats.total_quiz_attempts"
            icon="chart-bar"
            color="purple"
          />
          <StatsCard
            title="Average Quiz Score"
            :value="`${stats.average_quiz_score || 0}%`"
            icon="trophy"
            color="orange"
          />
        </div>

        <!-- Subscription Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Current Plan"
            :value="user.current_subscription?.plan?.name || 'Free'"
            icon="credit-card"
            color="indigo"
          />
          <StatsCard
            title="Subscription Status"
            :value="user.current_subscription?.status || 'None'"
            icon="status-online"
            :color="getSubscriptionStatusColor(user.current_subscription?.status)"
          />
          <StatsCard
            title="AI Requests This Month"
            :value="stats.ai_requests_this_month || 0"
            icon="sparkles"
            color="pink"
          />
          <StatsCard
            title="Total Payments"
            :value="`${user.currency || '₦'}${stats.total_payments || 0}`"
            icon="cash"
            color="emerald"
          />
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- User Information -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
              </div>
              <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ user.phone || 'Not provided' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getRoleBadgeClass(user.roles[0]?.name)"
                      >
                        {{ user.roles[0]?.name }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      >
                        {{ user.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                    <dd class="mt-1">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="user.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                      >
                        {{ user.email_verified_at ? 'Verified' : 'Pending' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Registered</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(user.created_at) }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Subscription Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Subscription Information</h3>
              </div>
              <div class="p-6">
                <div v-if="user.current_subscription" class="space-y-4">
                  <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Current Plan</dt>
                      <dd class="mt-1 text-sm font-semibold text-gray-900">
                        {{ user.current_subscription.plan?.name || 'Unknown Plan' }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Status</dt>
                      <dd class="mt-1">
                        <span
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getSubscriptionStatusClass(user.current_subscription.status)"
                        >
                          {{ user.current_subscription.status }}
                        </span>
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Started</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ formatDate(user.current_subscription.starts_at) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Expires</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ formatDate(user.current_subscription.ends_at) }}
                        <span class="text-xs text-gray-500 ml-1">
                          ({{ getDaysRemaining(user.current_subscription) }} days left)
                        </span>
                      </dd>
                    </div>
                    <div v-if="user.current_subscription.trial_ends_at">
                      <dt class="text-sm font-medium text-gray-500">Trial Ends</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ formatDate(user.current_subscription.trial_ends_at) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        <span v-if="user.current_subscription.card_type">
                          {{ user.current_subscription.card_type }} •••• {{ user.current_subscription.last4 }}
                        </span>
                        <span v-else class="text-gray-500">Not set</span>
                      </dd>
                    </div>
                  </dl>

                  <!-- Plan Features -->
                  <div v-if="user.current_subscription.plan">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Plan Features</h4>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="feature in user.current_subscription.plan.features || []"
                        :key="feature"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        {{ feature }}
                      </span>
                    </div>
                  </div>

                  <!-- Plan Limits -->
                  <div v-if="user.current_subscription.plan" class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                      <dt class="font-medium text-gray-500">Max Courses</dt>
                      <dd class="text-gray-900">
                        {{ user.current_subscription.plan.max_courses === -1 ? 'Unlimited' : user.current_subscription.plan.max_courses }}
                      </dd>
                    </div>
                    <div>
                      <dt class="font-medium text-gray-500">AI Requests</dt>
                      <dd class="text-gray-900">
                        {{ user.current_subscription.plan.max_ai_requests_per_month === -1 ? 'Unlimited' : user.current_subscription.plan.max_ai_requests_per_month }}/month
                      </dd>
                    </div>
                    <div v-if="user.current_subscription.plan.max_students">
                      <dt class="font-medium text-gray-500">Max Students</dt>
                      <dd class="text-gray-900">
                        {{ user.current_subscription.plan.max_students === -1 ? 'Unlimited' : user.current_subscription.plan.max_students }}
                      </dd>
                    </div>
                    <div v-if="user.current_subscription.plan.max_tutors">
                      <dt class="font-medium text-gray-500">Max Tutors</dt>
                      <dd class="text-gray-900">
                        {{ user.current_subscription.plan.max_tutors === -1 ? 'Unlimited' : user.current_subscription.plan.max_tutors }}
                      </dd>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center text-gray-500 py-4">
                  No active subscription
                </div>
              </div>
            </div>

            <!-- Student Profile (Conditional) -->
            <div v-if="user.student_profile" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Profile</h3>
              </div>
              <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Exam Board</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.student_profile.exam_board?.name || 'Not set' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Current Level</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">
                      {{ user.student_profile.current_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Target Level</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">
                      {{ user.student_profile.target_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Target Completion</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(user.student_profile.target_completion_date) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Weekly Study Hours</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.student_profile.weekly_study_hours }} hours
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Current Grade</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.student_profile.current_grade ? `${user.student_profile.current_grade}%` : 'Not set' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Target Grade</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.student_profile.target_grade ? `${user.student_profile.target_grade}%` : 'Not set' }}
                    </dd>
                  </div>
                </dl>

                <!-- Learning Goals -->
                <div class="mt-6" v-if="user.student_profile.learning_goals?.length">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Learning Goals</h4>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="goal in user.student_profile.learning_goals"
                      :key="goal"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ getLearningGoalLabel(goal) }}
                    </span>
                  </div>
                </div>

                <!-- Learning Preferences -->
                <div class="mt-6" v-if="user.student_profile.learning_preferences?.length">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Learning Preferences</h4>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="preference in user.student_profile.learning_preferences"
                      :key="preference"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                    >
                      {{ getLearningPreferenceLabel(preference) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tutor Profile (Conditional) -->
            <div v-if="user.tutor_profile" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tutor Profile</h3>
              </div>
              <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Qualification</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.qualification || 'Not specified' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Experience</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.years_of_experience }} years
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Hourly Rate</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.hourly_rate ? `₦${user.tutor_profile.hourly_rate}` : 'Not set' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="user.tutor_profile.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                      >
                        {{ user.tutor_profile.is_verified ? 'Verified' : 'Pending Verification' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Online Status</dt>
                    <dd class="mt-1">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="user.tutor_profile.is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                      >
                        {{ user.tutor_profile.is_online ? 'Online' : 'Offline' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Rating</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.rating || 'No ratings' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Total Reviews</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.total_reviews || 0 }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Students</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ user.tutor_profile.active_students_count || 0 }} / {{ user.tutor_profile.max_students }}
                    </dd>
                  </div>
                </dl>

                <!-- Specialties -->
                <div class="mt-6" v-if="user.tutor_profile.specialties?.length">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Specialties</h4>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="specialty in user.tutor_profile.specialties"
                      :key="specialty"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                    >
                      {{ getTutorSpecialtyLabel(specialty) }}
                    </span>
                  </div>
                </div>

                <!-- Bio -->
                <div class="mt-6" v-if="user.tutor_profile.bio">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Bio</h4>
                  <p class="text-sm text-gray-700">{{ user.tutor_profile.bio }}</p>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                  <Link
                    :href="route('admin.users.login-history', user.id)"
                    class="text-sm text-blue-600 hover:text-blue-500"
                  >
                    View All
                  </Link>
                </div>
              </div>
              <div class="p-6">
                <div v-if="user.login_histories?.length" class="space-y-4">
                  <div
                    v-for="login in user.login_histories"
                    :key="login.id"
                    class="flex items-center justify-between py-2"
                  >
                    <div class="flex items-center">
                      <div
                        class="w-2 h-2 rounded-full mr-3"
                        :class="login.was_successful ? 'bg-green-400' : 'bg-red-400'"
                      ></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          {{ login.device_type }} • {{ login.browser }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ login.ip_address }} • {{ formatDateTime(login.login_at) }}
                        </p>
                      </div>
                    </div>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="login.was_successful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                      {{ login.was_successful ? 'Success' : 'Failed' }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center text-gray-500 py-4">
                  No recent login activity
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6 space-y-3">
                <button
                  @click="toggleStatus"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  {{ user.is_active ? 'Deactivate' : 'Activate' }} User
                </button>
                <Link
                  :href="route('admin.users.login-history', user.id)"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  View Login History
                </Link>
                <Link
                  :href="route('admin.users.payment-history', user.id)"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  View Payment History
                </Link>
                <button
                  v-if="user.is_active"
                  @click="impersonate"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700"
                >
                  Impersonate User
                </button>
                <button
                  v-if="user.current_subscription"
                  @click="cancelSubscription"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  Cancel Subscription
                </button>
              </div>
            </div>

            <!-- Login Stats -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Login Statistics</h3>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <p class="text-sm font-medium text-gray-500">Total Logins</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.total_logins || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Success Rate</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.success_rate || 0 }}%
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Consecutive Days</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.consecutive_days || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Unique IPs</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.unique_ips || 0 }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Subscription History -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Subscription History</h3>
              </div>
              <div class="p-6">
                <div v-if="user.subscriptions?.length" class="space-y-3">
                  <div
                    v-for="subscription in user.subscriptions.slice(0, 3)"
                    :key="subscription.id"
                    class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ subscription.plan?.name || 'Unknown Plan' }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ formatDate(subscription.starts_at) }} - {{ formatDate(subscription.ends_at) }}
                      </p>
                    </div>
                    <span
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="getSubscriptionStatusClass(subscription.status)"
                    >
                      {{ subscription.status }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center text-gray-500 py-4">
                  No subscription history
                </div>
                <Link
                  v-if="user.subscriptions?.length > 3"
                  :href="route('admin.users.subscription-history', user.id)"
                  class="mt-3 block text-center text-sm text-blue-600 hover:text-blue-500"
                >
                  View All Subscriptions
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'

const props = defineProps({
  user: Object,
  stats: Object,
  loginStats: Object,
})

const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-red-100 text-red-800',
    tutor: 'bg-blue-100 text-blue-800',
    student: 'bg-green-100 text-green-800',
    organization: 'bg-purple-100 text-purple-800',
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getSubscriptionStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    canceled: 'bg-red-100 text-red-800',
    expired: 'bg-gray-100 text-gray-800',
    past_due: 'bg-yellow-100 text-yellow-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getSubscriptionStatusColor = (status) => {
  const colors = {
    active: 'green',
    canceled: 'red',
    expired: 'gray',
    past_due: 'yellow',
  }
  return colors[status] || 'gray'
}

const getLearningGoalLabel = (goal) => {
  const goals = {
    exam_preparation: 'Exam Preparation',
    career_advancement: 'Career Advancement',
    skill_development: 'Skill Development',
    academic_studies: 'Academic Studies',
    personal_interest: 'Personal Interest',
    certification: 'Get Certified',
  }
  return goals[goal] || goal
}

const getLearningPreferenceLabel = (preference) => {
  const preferences = {
    visual: 'Visual',
    interactive: 'Interactive',
    reading: 'Reading',
    video: 'Video',
    audio: 'Audio',
    social: 'Social Learning',
  }
  return preferences[preference] || preference
}

const getTutorSpecialtyLabel = (specialty) => {
  const specialties = {
    mathematics: 'Mathematics',
    science: 'Science',
    programming: 'Programming',
    languages: 'Languages',
    business: 'Business',
    arts: 'Arts',
    test_prep: 'Test Prep',
    music: 'Music',
    sports: 'Sports',
  }
  return specialties[specialty] || specialty
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getDaysRemaining = (subscription) => {
  if (!subscription.ends_at) return 0
  const endDate = new Date(subscription.ends_at)
  const today = new Date()
  const diffTime = endDate - today
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

const toggleStatus = () => {
  if (confirm(`Are you sure you want to ${props.user.is_active ? 'deactivate' : 'activate'} this user?`)) {
    router.patch(route('admin.users.toggle-status', props.user.id))
  }
}

const impersonate = () => {
  if (confirm('Are you sure you want to impersonate this user?')) {
    router.post(route('admin.users.impersonate', props.user.id))
  }
}

const cancelSubscription = () => {
  if (confirm('Are you sure you want to cancel this user\'s subscription?')) {
    router.post(route('admin.subscriptions.cancel', props.user.id))
  }
}
</script>
