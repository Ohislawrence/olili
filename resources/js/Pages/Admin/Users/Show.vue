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

        <!-- Enrollment Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Enrollment"
            :value="stats.total_enrollments"
            icon="academic-cap"
            color="blue"
          />
          <StatsCard
            title="Active Courses"
            :value="stats.active_enrollments"
            icon="academic-cap"
            color="blue"
          />
          <StatsCard
            title="Completed Enrollment"
            :value="stats.completed_enrollments"
            icon="check-circle"
            color="green"
          />
          <StatsCard
            title="Dropped Courses"
            :value="stats.dropped_enrollments"
            icon="x-circle"
            color="red"
          />
        </div>

        <!-- Study Time & Progress Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Study Hours"
            :value="stats.total_study_hours"
            icon="clock"
            color="purple"
            :description="`${stats.total_study_minutes} minutes`"
          />
          <StatsCard
            title="Avg Daily Study Hours"
            :value="stats.average_daily_study_hours"
            icon="calendar-days"
            color="indigo"
          />
          <StatsCard
            title="Weekly Study Hours"
            :value="stats.weekly_study_hours"
            icon="chart-bar"
            color="blue"
          />
          <StatsCard
            title="Learning Streak"
            :value="`${stats.streak_days} days`"
            icon="fire"
            color="orange"
          />
        </div>

        <!-- Completion & Efficiency Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Completion Rate"
            :value="`${stats.enrollment_completion_rate || 0}%`"
            icon="trophy"
            color="green"
            :description="`${stats.completed_enrollments}/${stats.total_enrollments} courses`"
          />
          <StatsCard
            title="Avg Course Completion"
            :value="`${stats.average_course_completion || 0}%`"
            icon="sparkles"
            color="emerald"
          />
          <StatsCard
            title="Completed Topics"
            :value="stats.completed_topics || 0"
            icon="document-check"
            color="teal"
          />
          <StatsCard
            title="Average Score"
            :value="stats.average_score"
            icon="star"
            color="yellow"
            :description="`From ${stats.completed_items || 0} items`"
          />
        </div>

        <!-- Efficiency & Activity Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Efficiency Score"
            :value="stats.efficiency_score"
            icon="bolt"
            color="pink"
            :description="stats.study_efficiency_rating"
          />
          <StatsCard
            title="Minutes Per Item"
            :value="stats.minutes_per_item"
            icon="clock"
            color="purple"
          />
          <StatsCard
            title="Completion Rate"
            :value="stats.completion_rate"
            icon="arrow-trending-up"
            color="blue"
            description="items per session"
          />
          <StatsCard
            title="Weekly Activity"
            :value="`${stats.weekly_activity_days} days`"
            icon="calendar-days"
            color="indigo"
          />
        </div>

        <!-- Certificate Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Active Certificates"
            :value="stats.active_certificates"
            icon="check-badge"
            color="emerald"
          />
          <StatsCard
            title="Expired Certificates"
            :value="stats.expired_certificates"
            icon="clock"
            color="yellow"
          />
          <StatsCard
            title="Recent Certificates"
            :value="stats.recent_certificates_30d"
            icon="newspaper"
            color="indigo"
            description="Last 30 days"
          />
          <StatsCard
            title="Certificate Downloads"
            :value="stats.total_certificate_downloads"
            icon="arrow-down-tray"
            color="pink"
          />
        </div>

        <!-- Subscription & Financial Stats -->
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
            title="Remaining Study Hours"
            :value="stats.estimated_remaining_hours"
            icon="clock"
            color="orange"
          />
          <StatsCard
            title="Total Payments"
            :value="`${user.currency || '₦'}${stats.total_payments || 0}`"
            icon="cash"
            color="emerald"
          />
        </div>

        <!-- Last Active & Most Active Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <StatsCard
            title="Last Active"
            :value="formatDateTime(stats.last_active_date) || 'Never'"
            icon="calendar"
            color="blue"
          />
          <StatsCard
            title="Most Active Day"
            :value="stats.progress_breakdown?.weekly_summary?.most_active_day || 'No data'"
            icon="chart-bar"
            color="purple"
            :description="`${stats.progress_breakdown?.weekly_summary?.total_days_active || 0} active days this week`"
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
                      {{ user.last_login_at ? formatDateTime(user.last_login_at) : 'Never' }}
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

            <!-- Course Progress Details -->
            <div v-if="stats.course_progress && stats.course_progress.length > 0" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Progress</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div
                    v-for="course in stats.course_progress"
                    :key="course.course_id"
                    class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
                  >
                    <div class="flex justify-between items-start mb-2">
                      <div>
                        <h4 class="font-medium text-gray-900">{{ course.course_title }}</h4>
                        <p class="text-sm text-gray-500">Course ID: {{ course.course_id }}</p>
                      </div>
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                        :class="getProgressStatusClass(course.overall_completion)"
                      >
                        {{ course.overall_completion }}% Complete
                      </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                      <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <span>{{ course.overall_completion }}%</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                          class="bg-green-600 h-2 rounded-full transition-all duration-300"
                          :style="{ width: `${course.overall_completion}%` }"
                        ></div>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                      <div>
                        <p class="text-gray-500">Modules</p>
                        <p class="font-medium text-gray-900">{{ course.module_completion }}%</p>
                      </div>
                      <div>
                        <p class="text-gray-500">Topics</p>
                        <p class="font-medium text-gray-900">{{ course.topic_completion }}%</p>
                      </div>
                      <div>
                        <p class="text-gray-500">Time Spent</p>
                        <p class="font-medium text-gray-900">{{ course.time_spent_hours }}h</p>
                      </div>
                      <div>
                        <p class="text-gray-500">Remaining</p>
                        <p class="font-medium text-gray-900">{{ course.estimated_remaining_hours }}h</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Weekly Progress Breakdown -->
            <div v-if="stats.weekly_progress && Object.keys(stats.weekly_progress).length > 0" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Weekly Study Activity</h3>
              </div>
              <div class="p-6">
                <div class="space-y-3 mb-6">
                  <div class="grid grid-cols-7 gap-2">
                    <div
                      v-for="(day, date) in stats.weekly_progress"
                      :key="date"
                      class="text-center"
                    >
                      <div class="text-xs text-gray-500 mb-1">
                        {{ formatDay(date) }}
                      </div>
                      <div
                        class="h-20 rounded-lg flex flex-col justify-end p-1"
                        :class="getActivityBarColor(day.study_time_hours)"
                        :title="`${day.study_time_hours} hours, ${day.activities} activities`"
                      >
                        <div
                          class="bg-current rounded w-full"
                          :style="{ height: `${getBarHeight(day.study_time_hours)}%` }"
                        ></div>
                      </div>
                      <div class="text-xs mt-1 font-medium">
                        {{ day.study_time_hours }}h
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Total Weekly Hours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.weekly_study_hours }}</p>
                  </div>
                  <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600">Active Days</p>
                    <p class="text-2xl font-bold text-blue-900">{{ stats.weekly_activity_days }}</p>
                  </div>
                  <div class="bg-emerald-50 p-4 rounded-lg">
                    <p class="text-sm text-emerald-600">Avg Daily Hours</p>
                    <p class="text-2xl font-bold text-emerald-900">{{ stats.average_daily_study_hours }}</p>
                  </div>
                  <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-600">Most Active</p>
                    <p class="text-2xl font-bold text-purple-900">{{ stats.progress_breakdown?.weekly_summary?.most_active_day || 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Efficiency Metrics -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Learning Efficiency</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                  <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                      {{ stats.efficiency_score }}
                    </div>
                    <div class="text-sm text-gray-600">Efficiency Score</div>
                    <div class="mt-2">
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                        :class="getEfficiencyRatingClass(stats.study_efficiency_rating)"
                      >
                        {{ stats.study_efficiency_rating }}
                      </span>
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                      {{ stats.minutes_per_item }}
                    </div>
                    <div class="text-sm text-gray-600">Minutes Per Item</div>
                    <div class="mt-2 text-xs">
                      <span :class="getTimePerItemClass(stats.minutes_per_item)">
                        {{ getTimePerItemRating(stats.minutes_per_item) }}
                      </span>
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                      {{ stats.completion_rate }}
                    </div>
                    <div class="text-sm text-gray-600">Items Per Session</div>
                    <div class="mt-2 text-xs">
                      <span :class="getCompletionRateClass(stats.completion_rate)">
                        {{ getCompletionRateRating(stats.completion_rate) }}
                      </span>
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                      {{ stats.streak_days }}
                    </div>
                    <div class="text-sm text-gray-600">Learning Streak</div>
                    <div class="mt-2">
                      <span
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-orange-100 text-orange-800"
                      >
                        {{ getStreakMessage(stats.streak_days) }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                      <span>Study Efficiency</span>
                      <span>{{ stats.efficiency_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div
                        class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300"
                        :style="{ width: `${stats.efficiency_score}%` }"
                      ></div>
                    </div>
                  </div>

                  <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                      <span>Learning Consistency</span>
                      <span>{{ stats.weekly_activity_days }}/7 days</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div
                        class="bg-gradient-to-r from-green-500 to-emerald-600 h-3 rounded-full transition-all duration-300"
                        :style="{ width: `${(stats.weekly_activity_days / 7) * 100}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enrollment Status Distribution -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Enrollment Status</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                  <div
                    v-for="(count, status) in stats.enrollment_status_distribution"
                    :key="status"
                    class="text-center p-4 rounded-lg"
                    :class="getEnrollmentStatusBgClass(status)"
                  >
                    <div class="text-2xl font-bold text-gray-900">{{ count }}</div>
                    <div class="text-sm capitalize" :class="getEnrollmentStatusTextClass(status)">
                      {{ status }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                      {{ stats.enrollment_status_distribution[status + '_percent'] || 0 }}%
                    </div>
                  </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="flex items-end h-24 mt-4 space-x-1">
                  <div
                    v-for="(count, status) in stats.enrollment_status_distribution"
                    :key="status"
                    class="flex-1 flex flex-col items-center"
                  >
                    <div
                      class="w-full rounded-t"
                      :class="getEnrollmentStatusBarClass(status)"
                      :style="{ height: `${getStatusBarHeight(count, stats.total_enrollments)}%` }"
                      :title="`${status}: ${count} (${stats.enrollment_status_distribution[status + '_percent'] || 0}%)`"
                    ></div>
                    <div class="text-xs text-gray-500 mt-2 capitalize truncate w-full text-center">
                      {{ status }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Certificate Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-900">Certificate Information</h3>
                  <Link
                    :href="route('admin.users.certificates', user.id)"
                    class="text-sm text-blue-600 hover:text-blue-500"
                  >
                    View All Certificates
                  </Link>
                </div>
              </div>
              <div class="p-6">
                <!-- Certificate Stats Summary -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                  <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_certificates || 0 }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                  </div>
                  <div class="text-center p-4 bg-emerald-50 rounded-lg">
                    <div class="text-2xl font-bold text-emerald-900">{{ stats.active_certificates || 0 }}</div>
                    <div class="text-sm text-emerald-600">Active</div>
                  </div>
                  <div class="text-center p-4 bg-amber-50 rounded-lg">
                    <div class="text-2xl font-bold text-amber-900">{{ stats.expired_certificates || 0 }}</div>
                    <div class="text-sm text-amber-600">Expired</div>
                  </div>
                  <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-900">{{ stats.total_certificate_downloads || 0 }}</div>
                    <div class="text-sm text-blue-600">Downloads</div>
                  </div>
                </div>

                <!-- Recent Certificates -->
                <div v-if="user.certificates?.length" class="space-y-4">
                  <h4 class="text-sm font-medium text-gray-500">Recent Certificates</h4>
                  <div
                    v-for="certificate in user.certificates.slice(0, 3)"
                    :key="certificate.id"
                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
                  >
                    <div class="flex items-center space-x-4">
                      <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold">
                        C
                      </div>
                      <div>
                        <div class="font-medium text-gray-900">{{ certificate.course?.title || 'Unknown Course' }}</div>
                        <div class="text-sm text-gray-500">
                          {{ certificate.certificate_number }} • Issued {{ formatDate(certificate.issue_date) }}
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                        :class="getCertificateStatusClass(certificate.status)"
                      >
                        {{ certificate.status }}
                      </span>
                      <Link
                        :href="route('admin.certificates.show', certificate.id)"
                        class="text-blue-600 hover:text-blue-900"
                        title="View Certificate"
                      >
                        <EyeIcon class="h-5 w-5" />
                      </Link>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center text-gray-500 py-4">
                  No certificates issued yet
                </div>

                <!-- Certificate Eligibility -->
                <div v-if="user.student_profile && user.courses?.length" class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-500 mb-4">Certificate Eligibility</h4>
                  <div class="space-y-3">
                    <div
                      v-for="course in user.courses"
                      :key="course.id"
                      class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                      <div>
                        <div class="font-medium text-gray-900">{{ course.title }}</div>
                        <div class="text-sm text-gray-500">
                          {{ course.progress_percentage }}% complete • {{ course.status }}
                        </div>
                      </div>
                      <div class="text-right">
                        <div v-if="getCourseCertificateStatus(course)" class="text-sm">
                          <span :class="getCertificateStatusTextClass(getCourseCertificateStatus(course))">
                            {{ getCourseCertificateStatus(course) }}
                          </span>
                        </div>
                        <button
                          v-if="isCourseEligibleForCertificate(course) && !getExistingCertificate(course)"
                          @click="generateCertificate(course)"
                          class="mt-1 text-sm text-emerald-600 hover:text-emerald-900 font-medium"
                        >
                          Issue Certificate
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
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
                  :href="route('admin.users.certificates', user.id)"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Manage Certificates
                </Link>
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
                <!-- Impersonation banner (show when impersonating) -->
                <div
                  v-if="$page.props.auth.impersonating"
                  class="bg-yellow-100 border-b border-yellow-200 py-2 px-4"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600 mr-2" />
                      <span class="text-sm text-yellow-800">
                        You are currently impersonating {{ $page.props.auth.user.name }}
                      </span>
                    </div>
                    <button
                      @click="leaveImpersonation"
                      class="inline-flex items-center px-3 py-1 text-xs font-medium rounded bg-yellow-600 text-white hover:bg-yellow-700"
                    >
                      <ArrowLeftIcon class="h-3 w-3 mr-1" />
                      Stop Impersonating
                    </button>
                  </div>
                </div>
                <button
                  v-if="user.current_subscription"
                  @click="cancelSubscription"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  Cancel Subscription
                </button>
              </div>
            </div>

            <!-- Certificate Quick Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Certificate Actions</h3>
              </div>
              <div class="p-6 space-y-3">
                <Link
                  :href="route('admin.certificates.create', { user_id: user.id })"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
                >
                  Issue New Certificate
                </Link>
                <button
                  v-if="hasCompletedCoursesWithoutCertificates"
                  @click="generateAllEligibleCertificates"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  Generate All Eligible
                </button>
                <button
                  @click="exportCertificates"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Export Certificates
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
                    {{ loginStats.total_logins_30d || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Success Rate</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.success_rate_30d || 0 }}%
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Consecutive Days</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.consecutive_days || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Unique Locations</p>
                  <p class="text-2xl font-semibold text-gray-900">
                    {{ loginStats.unique_locations || 0 }}
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
import { EyeIcon, ExclamationTriangleIcon, ArrowLeftIcon, ChartBarIcon, CalendarDaysIcon, ClockIcon, FireIcon, BoltIcon, StarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  user: Object,
  stats: Object,
  loginStats: Object,
})

// Helper methods for progress
const getProgressStatusClass = (percentage) => {
  if (percentage >= 90) return 'bg-emerald-100 text-emerald-800'
  if (percentage >= 70) return 'bg-green-100 text-green-800'
  if (percentage >= 50) return 'bg-yellow-100 text-yellow-800'
  if (percentage >= 30) return 'bg-orange-100 text-orange-800'
  return 'bg-red-100 text-red-800'
}

const formatDay = (dateString) => {
  const date = new Date(dateString)
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  return days[date.getDay()]
}

const getActivityBarColor = (hours) => {
  if (hours >= 4) return 'bg-gradient-to-t from-green-600 to-green-500 text-green-600'
  if (hours >= 2) return 'bg-gradient-to-t from-blue-600 to-blue-500 text-blue-600'
  if (hours >= 1) return 'bg-gradient-to-t from-yellow-600 to-yellow-500 text-yellow-600'
  if (hours > 0) return 'bg-gradient-to-t from-gray-600 to-gray-500 text-gray-600'
  return 'bg-gray-100 text-gray-300'
}

const getBarHeight = (hours) => {
  const maxHours = Math.max(...Object.values(props.stats.weekly_progress || {}).map(d => d.study_time_hours || 0), 1)
  return Math.min((hours / maxHours) * 100, 100)
}

const getEfficiencyRatingClass = (rating) => {
  const classes = {
    'Excellent': 'bg-emerald-100 text-emerald-800',
    'Good': 'bg-green-100 text-green-800',
    'Average': 'bg-yellow-100 text-yellow-800',
    'Needs Improvement': 'bg-orange-100 text-orange-800',
    'Poor': 'bg-red-100 text-red-800',
  }
  return classes[rating] || 'bg-gray-100 text-gray-800'
}

const getTimePerItemClass = (minutes) => {
  if (minutes <= 5) return 'text-emerald-600'
  if (minutes <= 10) return 'text-green-600'
  if (minutes <= 20) return 'text-yellow-600'
  if (minutes <= 30) return 'text-orange-600'
  return 'text-red-600'
}

const getTimePerItemRating = (minutes) => {
  if (minutes <= 5) return 'Very Fast'
  if (minutes <= 10) return 'Fast'
  if (minutes <= 20) return 'Average'
  if (minutes <= 30) return 'Slow'
  return 'Very Slow'
}

const getCompletionRateClass = (rate) => {
  if (rate >= 4) return 'text-emerald-600'
  if (rate >= 3) return 'text-green-600'
  if (rate >= 2) return 'text-yellow-600'
  if (rate >= 1) return 'text-orange-600'
  return 'text-red-600'
}

const getCompletionRateRating = (rate) => {
  if (rate >= 4) return 'Excellent'
  if (rate >= 3) return 'Good'
  if (rate >= 2) return 'Average'
  if (rate >= 1) return 'Low'
  return 'Very Low'
}

const getStreakMessage = (days) => {
  if (days >= 30) return 'Consistent Learner!'
  if (days >= 14) return 'Great Commitment!'
  if (days >= 7) return 'Good Routine'
  if (days >= 3) return 'Getting There'
  return 'Just Started'
}

const getEnrollmentStatusBgClass = (status) => {
  const classes = {
    active: 'bg-blue-50',
    enrolled: 'bg-blue-50',
    completed: 'bg-emerald-50',
    dropped: 'bg-red-50',
    pending: 'bg-yellow-50',
    suspended: 'bg-gray-50',
  }
  return classes[status] || 'bg-gray-50'
}

const getEnrollmentStatusTextClass = (status) => {
  const classes = {
    active: 'text-blue-700',
    enrolled: 'text-blue-700',
    completed: 'text-emerald-700',
    dropped: 'text-red-700',
    pending: 'text-yellow-700',
    suspended: 'text-gray-700',
  }
  return classes[status] || 'text-gray-700'
}

const getEnrollmentStatusBarClass = (status) => {
  const classes = {
    active: 'bg-blue-500',
    enrolled: 'bg-blue-400',
    completed: 'bg-emerald-500',
    dropped: 'bg-red-500',
    pending: 'bg-yellow-500',
    suspended: 'bg-gray-500',
  }
  return classes[status] || 'bg-gray-500'
}

const getStatusBarHeight = (count, total) => {
  if (total === 0) return 0
  return Math.max((count / total) * 100, 10) // Minimum 10% for visibility
}

// Keep existing methods
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

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'Never'
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  if (diffDays === 0) {
    return 'Today, ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
  } else if (diffDays === 1) {
    return 'Yesterday, ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
  } else if (diffDays < 7) {
    return `${diffDays} days ago`
  } else {
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }
}



const getCertificateStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    expired: 'bg-amber-100 text-amber-800',
    revoked: 'bg-red-100 text-red-800',
    pending: 'bg-blue-100 text-blue-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getCertificateStatusTextClass = (status) => {
  const classes = {
    'Certificate Issued': 'text-emerald-600',
    'Eligible': 'text-blue-600',
    'In Progress': 'text-amber-600',
    'Not Eligible': 'text-gray-500',
    'No Capstone': 'text-red-600',
  }
  return classes[status] || 'text-gray-500'
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



const getDaysRemaining = (subscription) => {
  if (!subscription.ends_at) return 0
  const endDate = new Date(subscription.ends_at)
  const today = new Date()
  const diffTime = endDate - today
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

// Helper methods for certificates
const getExistingCertificate = (course) => {
  return props.user.certificates?.find(cert => cert.course_id === course.id)
}

const getCourseCertificateStatus = (course) => {
  const existingCert = getExistingCertificate(course)
  if (existingCert) {
    return 'Certificate Issued'
  }

  if (course.status !== 'completed') {
    return 'In Progress'
  }

  if (!course.capstone_project?.is_approved) {
    return 'No Capstone'
  }

  const totalModules = course.modules?.length || 0
  const completedModules = course.modules?.filter(m => m.is_completed).length || 0

  if (totalModules === 0 || completedModules < totalModules) {
    return 'Not Eligible'
  }

  if (course.progress_percentage >= 70) {
    return 'Eligible'
  }

  return 'Not Eligible'
}

const isCourseEligibleForCertificate = (course) => {
  return getCourseCertificateStatus(course) === 'Eligible'
}

const hasCompletedCoursesWithoutCertificates = () => {
  if (!props.user.courses) return false
  return props.user.courses.some(course =>
    isCourseEligibleForCertificate(course) && !getExistingCertificate(course)
  )
}

const generateCertificate = (course) => {
  if (confirm(`Issue certificate for "${course.title}" to ${props.user.name}?`)) {
    router.post(route('admin.certificates.generate'), {
      user_id: props.user.id,
      course_id: course.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Reload the page to show the new certificate
        router.reload({ only: ['user', 'stats'] })
      }
    })
  }
}

const generateAllEligibleCertificates = () => {
  const eligibleCourses = props.user.courses?.filter(course =>
    isCourseEligibleForCertificate(course) && !getExistingCertificate(course)
  ) || []

  if (eligibleCourses.length === 0) {
    alert('No eligible courses found for certificate generation.')
    return
  }

  if (confirm(`Generate certificates for ${eligibleCourses.length} eligible courses?`)) {
    router.post(route('admin.certificates.batch-generate'), {
      user_id: props.user.id,
      course_ids: eligibleCourses.map(c => c.id)
    }, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['user', 'stats'] })
      }
    })
  }
}

const exportCertificates = () => {
  router.post(route('admin.certificates.export', props.user.id))
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

const leaveImpersonation = () => {
  router.post(route('admin.impersonate.leave'))
}

const cancelSubscription = () => {
  if (confirm('Are you sure you want to cancel this user\'s subscription?')) {
    router.post(route('admin.subscriptions.cancel', props.user.id))
  }
}
</script>
