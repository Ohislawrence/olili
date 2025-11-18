<template>
    <StudentLayout>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50 py-12">
    <Head title="Subscription Plans" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Current Subscription Info - Moved to top -->
      <div
        v-if="hasCurrentSubscription"
        class="bg-white rounded-xl shadow-sm border border-emerald-200 p-6 mb-12 max-w-4xl mx-auto"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-bold text-gray-900">
            Current Subscription
          </h3>
          <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800"
          >
            Active
          </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <p class="text-sm font-medium text-gray-600">Plan</p>
            <p class="font-bold text-emerald-700 text-lg">
              {{ currentSubscriptionPlan?.name || 'Unknown Plan' }}
            </p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Status</p>
            <p class="font-bold text-emerald-700 text-lg capitalize">
              {{ current_subscription.status }}
            </p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Renews On</p>
            <p class="font-bold text-emerald-700 text-lg">
              {{ formatDate(current_subscription.ends_at) }}
            </p>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-4">
          <Link
            :href="route('payment.history')"
            class="text-emerald-600 hover:text-emerald-700 font-semibold"
          >
            View Payment History
          </Link>
          <button
            @click="cancelSubscription"
            class="text-red-600 hover:text-red-700 font-semibold"
          >
            Cancel Subscription
          </button>
        </div>
      </div>

      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Choose Your Plan
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Select the perfect plan for your learning journey. All plans include access to our AI tutor and course materials.
        </p>
      </div>

      <!-- Billing Toggle -->
      <div class="flex justify-center mb-12">
        <div class="bg-white rounded-xl p-1 shadow-sm border border-gray-200">
          <button
            @click="billingCycle = 'monthly'"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200"
            :class="billingCycle === 'monthly'
              ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
          >
            Monthly
          </button>
          <button
            @click="billingCycle = 'yearly'"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200"
            :class="billingCycle === 'yearly'
              ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
          >
            Yearly
            <span class="ml-1 text-xs bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-medium">
              Save 10%
            </span>
          </button>
        </div>
      </div>

      <!-- Plans Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <div
          v-for="plan in plans"
          :key="plan.id"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-lg"
          :class="{
            'border-emerald-500 transform scale-105 shadow-lg': plan.is_popular,
            'border-gray-200': !plan.is_popular
          }"
        >
          <!-- Popular Badge -->
          <div
            v-if="plan.is_popular"
            class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold py-2.5 px-4 rounded-t-2xl text-center"
          >
            Most Popular
          </div>

          <div class="p-8">
            <!-- Plan Header -->
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">
                {{ plan.name }}
              </h3>
              <p class="text-gray-600 mb-4">
                {{ plan.description }}
              </p>
              <div class="mb-4">
                <span class="text-4xl font-bold text-gray-900">
                  {{ getPlanPrice(plan) }}
                </span>
                <span class="text-gray-600">
                  /{{ billingCycle === 'yearly' ? 'year' : 'month' }}
                </span>
              </div>
            </div>

            <!-- Features -->
            <ul class="space-y-3 mb-8">
              <li
                v-for="feature in getPlanFeatures(plan)"
                :key="feature"
                class="flex items-center"
              >
                <CheckIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                <span class="text-gray-700">{{ feature }}</span>
              </li>
              <li class="flex items-center">
                <CheckIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                <span class="text-gray-700">
                  {{ plan.max_courses === -1 ? 'Unlimited' : plan.max_courses }} Courses
                </span>
              </li>
              <li class="flex items-center">
                <CheckIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                <span class="text-gray-700">
                  {{ plan.max_ai_requests_per_month === -1 ? 'Unlimited' : plan.max_ai_requests_per_month }} AI Requests/Month
                </span>
              </li>
              <li
                v-if="plan.ai_grading"
                class="flex items-center"
              >
                <CheckIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                <span class="text-gray-700">AI Project Grading</span>
              </li>
              <li
                v-if="plan.priority_support"
                class="flex items-center"
              >
                <CheckIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                <span class="text-gray-700">Priority Support</span>
              </li>
            </ul>

            <!-- Action Button -->
            <button
              @click="subscribe(plan)"
              :disabled="isCurrentPlan(plan) || processing"
              class="w-full py-3.5 px-6 rounded-xl font-semibold text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm hover:shadow-md"
              :class="getButtonClasses(plan)"
            >
              <span v-if="processing && selectedPlanId === plan.id">
                <ArrowPathIcon class="h-5 w-5 animate-spin mx-auto" />
              </span>
              <span v-else>
                {{ getButtonText(plan) }}
              </span>
            </button>

            <!-- Current Plan Badge -->
            <div
              v-if="isCurrentPlan(plan)"
              class="text-center mt-4"
            >
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
                <CheckIcon class="h-4 w-4 mr-1" />
                Current Plan
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { CheckIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps({
  plans: Array,
  current_subscription: Object,
  paystack_public_key: String,
})

const billingCycle = ref('monthly')
const processing = ref(false)
const selectedPlanId = ref(null)

// Computed properties for safe access
const hasCurrentSubscription = computed(() => {
  return props.current_subscription !== null && props.current_subscription !== undefined
})

const currentSubscriptionPlan = computed(() => {
  if (!hasCurrentSubscription.value) return null

  // Try to get plan from relationship or find in plans array
  if (props.current_subscription.plan && props.current_subscription.plan.name) {
    return props.current_subscription.plan
  }

  // Find in plans array
  return props.plans.find(plan => plan.id === props.current_subscription.subscription_plan_id)
})

const getPlanPrice = (plan) => {
  const amount = billingCycle.value === 'yearly'
    ? plan.yearly_price || (plan.price * 12 * 0.9)
    : plan.monthly_price || plan.price

  return `${plan.currency} ${amount.toLocaleString()}`
}

const isCurrentPlan = (plan) => {
  if (!hasCurrentSubscription.value) {
    return false
  }
  return props.current_subscription.subscription_plan_id === plan.id
}

const getButtonClasses = (plan) => {
  if (isCurrentPlan(plan)) {
    return 'bg-gray-400 cursor-not-allowed'
  }
  if (plan.is_popular) {
    return 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:ring-emerald-500 shadow-md hover:shadow-lg'
  }
  return 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:ring-emerald-500 shadow-md hover:shadow-lg'
}

const getButtonText = (plan) => {
  if (isCurrentPlan(plan)) {
    return 'Current Plan'
  }

  if (plan.price === 0) {
    return 'Get Started Free'
  }

  const currentPlanPrice = currentSubscriptionPlan.value?.price || 0
  if (currentPlanPrice && plan.price > currentPlanPrice) {
    return 'Upgrade Now'
  }

  if (currentPlanPrice && plan.price < currentPlanPrice) {
    return 'Downgrade'
  }

  return billingCycle.value === 'yearly' ? 'Get Started - Yearly' : 'Get Started - Monthly'
}

const getPlanFeatures = (plan) => {
  if (plan.recommended_features && plan.recommended_features.length > 0) {
    return plan.recommended_features
  }

  const defaultFeatures = {
    free: ['Basic AI chat access', 'Limited course access', 'Community support'],
    basic: ['Enhanced AI assistance', 'More course access', 'Progress tracking'],
    pro: ['Unlimited AI learning', 'Full course library', 'Advanced explanations'],
    premium: ['Everything in Pro', 'Personalized study plans', 'Priority support'],
    starter: ['Create courses', 'Manage students', 'Basic analytics'],
    growth: ['Unlimited students', 'AI grading', 'Custom portal'],
    enterprise: ['SSO security', 'API access', 'Dedicated support']
  }

  return defaultFeatures[plan.tier] || ['Feature 1', 'Feature 2', 'Feature 3']
}

const getDaysRemaining = (subscription) => {
  if (!subscription.ends_at) return 0
  const endDate = new Date(subscription.ends_at)
  const today = new Date()
  const diffTime = endDate - today
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

const subscribe = async (plan) => {
  if (isCurrentPlan(plan)) return

  processing.value = true
  selectedPlanId.value = plan.id

  try {
    console.log('Processing plan:', plan.id, plan.name, 'Price:', plan.price)

    // Handle free plans differently
    if (plan.price === 0) {
      console.log('Handling free plan')
      const response = await router.post(route('subscription.change'), {
        plan_id: plan.id,
      }, {
        onFinish: () => {
          processing.value = false
          selectedPlanId.value = null
        },
        onError: (errors) => {
          console.error('Free plan subscription error:', errors)
          processing.value = false
          selectedPlanId.value = null
          alert('Failed to subscribe to free plan. Please try again.')
        },
        onSuccess: () => {
          console.log('Free plan subscription successful')
          // Page will reload with new subscription
        }
      })
    } else {
      // Paid plan - initialize subscription payment with metadata
      console.log('Initializing subscription payment for paid plan')

      const metadata = {
        billing_cycle: billingCycle.value,
        plan_tier: plan.tier,
        is_popular: plan.is_popular,
        change_type: hasCurrentSubscription.value ? 'change' : 'new'
      }

      const response = await router.post(route('payment.subscription.initialize'), {
        plan_id: plan.id,
        metadata: metadata
      }, {
        onFinish: () => {
          console.log('Subscription payment request finished')
          processing.value = false
          selectedPlanId.value = null
        },
        onError: (errors) => {
          console.error('Subscription payment initialization error:', errors)
          processing.value = false
          selectedPlanId.value = null
          alert('Failed to initialize payment. Please try again.')
        },
        onSuccess: (page) => {
          console.log('Subscription payment initialized successfully')
          // Inertia.location will handle the redirect to Paystack
        }
      })
    }

  } catch (error) {
    console.error('Subscription error:', error)
    processing.value = false
    selectedPlanId.value = null
    alert('An error occurred. Please try again.')
  }
}

const cancelSubscription = () => {
  if (confirm('Are you sure you want to cancel your subscription? You will lose access to premium features at the end of your billing period.')) {
    router.post(route('subscription.cancel'))
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
