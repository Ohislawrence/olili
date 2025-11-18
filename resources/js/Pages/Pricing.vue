<template>
  <AppLayout :title="`${userRole.charAt(0).toUpperCase() + userRole.slice(1)} Pricing`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ userRole.charAt(0).toUpperCase() + userRole.slice(1) }} Pricing Plans
        </h2>
        <div class="flex items-center space-x-4">
          <div class="text-sm text-gray-600">
            Current Plan:
            <span v-if="current_subscription" class="font-semibold text-emerald-600">
              {{ current_subscription.plan.name }}
            </span>
            <span v-else class="font-semibold text-gray-500">
              Free Tier
            </span>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Role Selection Tabs -->
        <div class="flex justify-center mb-12">
          <div class="bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
            <div class="flex space-x-1">
              <button
                v-for="role in availableRoles"
                :key="role"
                @click="switchRole(role)"
                class="px-6 py-3 text-sm font-medium rounded-md transition-all duration-200"
                :class="userRole === role
                  ? getRoleButtonClasses(role, true)
                  : getRoleButtonClasses(role, false)"
              >
                {{ role.charAt(0).toUpperCase() + role.slice(1) }} Plans
              </button>
            </div>
          </div>
        </div>

        <!-- Billing Toggle -->
        <div class="flex justify-center mb-12">
          <div class="bg-gray-100 rounded-lg p-1">
            <div class="flex items-center space-x-4">
              <span class="text-sm font-medium text-gray-700" :class="{ 'text-emerald-600': billingCycle === 'monthly' }">
                Monthly
              </span>
              <button
                @click="toggleBillingCycle"
                class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                :class="billingCycle === 'yearly' ? 'bg-emerald-600' : 'bg-gray-300'"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                  :class="billingCycle === 'yearly' ? 'translate-x-6' : 'translate-x-1'"
                />
              </button>
              <span class="text-sm font-medium text-gray-700" :class="{ 'text-emerald-600': billingCycle === 'yearly' }">
                Yearly
                <span class="text-xs text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full ml-2">
                  Save 10%
                </span>
              </span>
            </div>
          </div>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <div
            v-for="plan in filteredPlans"
            :key="plan.id"
            class="bg-white rounded-2xl shadow-lg border-2 transition-all duration-300 hover:shadow-xl"
            :class="getPlanCardClasses(plan)"
          >
            <!-- Popular Badge -->
            <div v-if="plan.tier === 'pro' || plan.tier === 'growth'" class="flex justify-center -mt-4">
              <span class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                Most Popular
              </span>
            </div>

            <div class="p-8">
              <!-- Plan Header -->
              <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                  {{ plan.name }}
                </h3>
                <p class="text-gray-600 text-sm">
                  {{ plan.description }}
                </p>
              </div>

              <!-- Price -->
              <div class="text-center mb-6">
                <div class="flex items-baseline justify-center">
                  <span class="text-4xl font-bold text-gray-900">
                    {{ getPlanPrice(plan) }}
                  </span>
                  <span class="text-gray-600 ml-2">
                    /{{ billingCycle === 'yearly' ? 'year' : 'month' }}
                  </span>
                </div>
                <p v-if="billingCycle === 'yearly' && plan.yearly_price" class="text-sm text-gray-500 mt-1">
                  {{ plan.currency }} {{ plan.monthly_price }}/month billed annually
                </p>
              </div>

              <!-- Current Plan Badge -->
              <div v-if="isCurrentPlan(plan)" class="flex justify-center mb-6">
                <span class="bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-1 rounded-full">
                  Current Plan
                </span>
              </div>

              <!-- Action Button -->
              <div class="mb-6">
                <button
                  @click="handlePlanAction(plan)"
                  class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                  :class="getPlanButtonClasses(plan)"
                  :disabled="processingPlan === plan.id"
                >
                  <span v-if="processingPlan === plan.id">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                  </span>
                  <span v-else>
                    {{ getPlanButtonText(plan) }}
                  </span>
                </button>
              </div>

              <!-- Features -->
              <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">
                  What's included:
                </h4>
                <ul class="space-y-3">
                  <li
                    v-for="feature in getPlanFeatures(plan)"
                    :key="feature"
                    class="flex items-start"
                  >
                    <svg class="h-5 w-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700 text-sm">{{ feature }}</span>
                  </li>
                </ul>
              </div>

              <!-- Limits -->
              <div v-if="hasPlanLimits(plan)" class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-3">
                  Plan Limits:
                </h4>
                <div class="space-y-2 text-sm text-gray-600">
                  <div v-if="plan.max_courses !== null" class="flex justify-between">
                    <span>Max Courses:</span>
                    <span class="font-medium">
                      {{ plan.max_courses === -1 ? 'Unlimited' : plan.max_courses }}
                    </span>
                  </div>
                  <div v-if="plan.max_students !== null" class="flex justify-between">
                    <span>Max Students:</span>
                    <span class="font-medium">
                      {{ plan.max_students === -1 ? 'Unlimited' : plan.max_students }}
                    </span>
                  </div>
                  <div v-if="plan.max_tutors !== null" class="flex justify-between">
                    <span>Max Tutors:</span>
                    <span class="font-medium">
                      {{ plan.max_tutors === -1 ? 'Unlimited' : plan.max_tutors }}
                    </span>
                  </div>
                  <div v-if="plan.max_ai_requests_per_month !== null" class="flex justify-between">
                    <span>AI Requests/Month:</span>
                    <span class="font-medium">
                      {{ plan.max_ai_requests_per_month === -1 ? 'Unlimited' : plan.max_ai_requests_per_month }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Free Plan Notice -->
        <div v-if="hasFreePlan" class="mt-12 text-center">
          <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 max-w-2xl mx-auto">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">
              Free Plan Available
            </h3>
            <p class="text-blue-800">
              Start with our free plan and upgrade anytime. All plans include basic features to get you started.
            </p>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
          <h3 class="text-2xl font-bold text-center text-gray-900 mb-8">
            Frequently Asked Questions
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div v-for="faq in faqs" :key="faq.question" class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <h4 class="font-semibold text-gray-900 mb-2">{{ faq.question }}</h4>
              <p class="text-gray-600 text-sm">{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  plans: Array,
  current_subscription: Object,
  user_role: String,
  paystack_public_key: String,
})

const billingCycle = ref('monthly')
const processingPlan = ref(null)
const userRole = ref(props.user_role)

const availableRoles = ['student', 'tutor', 'organization']

// Computed properties
const filteredPlans = computed(() => {
  return props.plans.filter(plan => plan.role === userRole.value)
})

const hasFreePlan = computed(() => {
  return filteredPlans.value.some(plan => plan.price === 0)
})

// Methods
const getPlanPrice = (plan) => {
  if (billingCycle.value === 'yearly' && plan.yearly_price) {
    return `${plan.currency} ${plan.yearly_price}`
  }
  return `${plan.currency} ${plan.monthly_price}`
}

const isCurrentPlan = (plan) => {
  if (!props.current_subscription) {
    return false
  }
  return props.current_subscription.subscription_plan_id === plan.id
}

const getPlanButtonText = (plan) => {
  if (isCurrentPlan(plan)) {
    return 'Current Plan'
  }

  if (plan.price === 0) {
    return 'Get Started Free'
  }

  const currentPlan = props.current_subscription?.plan
  if (currentPlan && plan.price > currentPlan.price) {
    return 'Upgrade Now'
  }

  if (currentPlan && plan.price < currentPlan.price) {
    return 'Downgrade'
  }

  return 'Get Started'
}

const getPlanButtonClasses = (plan) => {
  if (isCurrentPlan(plan)) {
    return 'bg-gray-300 text-gray-700 cursor-not-allowed'
  }

  if (plan.tier === 'pro' || plan.tier === 'growth' || plan.tier === 'business') {
    return 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 focus:ring-purple-500'
  }

  return 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500'
}

const getPlanCardClasses = (plan) => {
  const baseClasses = 'relative'

  if (isCurrentPlan(plan)) {
    return `${baseClasses} border-emerald-500 ring-2 ring-emerald-200`
  }

  if (plan.tier === 'pro' || plan.tier === 'growth' || plan.tier === 'business') {
    return `${baseClasses} border-purple-500 hover:border-purple-600`
  }

  return `${baseClasses} border-gray-200 hover:border-emerald-300`
}

const getRoleButtonClasses = (role, isActive) => {
  const baseClasses = 'transition-all duration-200'

  if (isActive) {
    switch (role) {
      case 'student': return `${baseClasses} bg-emerald-600 text-white shadow-md`
      case 'tutor': return `${baseClasses} bg-blue-600 text-white shadow-md`
      case 'organization': return `${baseClasses} bg-purple-600 text-white shadow-md`
      default: return `${baseClasses} bg-gray-600 text-white shadow-md`
    }
  }

  return `${baseClasses} text-gray-600 hover:text-gray-900 hover:bg-gray-100`
}

const getPlanFeatures = (plan) => {
  // Use the recommended features from the backend or fallback to default features
  if (plan.recommended_features && plan.recommended_features.length > 0) {
    return plan.recommended_features
  }

  // Fallback features based on role and tier
  const defaultFeatures = {
    student: {
      free: ['Basic AI chat access', 'Limited course creation', 'Community support'],
      basic: ['Enhanced AI assistance', 'More course creation', 'Progress tracking'],
      pro: ['Unlimited AI learning', 'Full course library', 'Advanced explanations'],
      premium: ['Everything in Pro', 'Personalized study plans', 'Priority support']
    },
    tutor: {
      starter: ['Create courses', 'Manage students', 'Basic analytics'],
      pro: ['Unlimited courses', 'AI teaching assistants', 'Payment integration'],
      business: ['Team collaboration', 'Video hosting', 'Branded pages']
    },
    organization: {
      starter: ['Student management', 'Basic dashboard', 'School-wide courses'],
      growth: ['Unlimited students', 'AI grading', 'Custom portal'],
      enterprise: ['SSO security', 'API access', 'Dedicated support']
    }
  }

  return defaultFeatures[plan.role]?.[plan.tier] || ['Feature 1', 'Feature 2', 'Feature 3']
}

const hasPlanLimits = (plan) => {
  return plan.max_courses !== null ||
         plan.max_students !== null ||
         plan.max_tutors !== null ||
         plan.max_ai_requests_per_month !== null
}

const toggleBillingCycle = () => {
  billingCycle.value = billingCycle.value === 'monthly' ? 'yearly' : 'monthly'
}

const switchRole = (role) => {
  userRole.value = role
  // You can also navigate to the role-specific pricing page
  router.get(route('pricing.role', { role }))
}

const handlePlanAction = async (plan) => {
  if (isCurrentPlan(plan)) {
    return // Do nothing for current plan
  }

  processingPlan.value = plan.id

  try {
    if (plan.price === 0) {
      // Handle free plan selection
      await selectFreePlan(plan)
    } else {
      // Initialize payment for paid plan
      await initializePayment(plan)
    }
  } catch (error) {
    console.error('Error handling plan action:', error)
  } finally {
    processingPlan.value = null
  }
}

const selectFreePlan = async (plan) => {
  try {
    const response = await router.post(route('subscription.change'), {
      plan_id: plan.id
    })
  } catch (error) {
    console.error('Error selecting free plan:', error)
  }
}

const initializePayment = async (plan) => {
  try {
    const response = await router.post(route('payment.subscription.initialize'), {
      plan_id: plan.id
    })
  } catch (error) {
    console.error('Error initializing payment:', error)
  }
}

// FAQ data
const faqs = [
  {
    question: "Can I change plans anytime?",
    answer: "Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately."
  },
  {
    question: "Is there a free trial?",
    answer: "Yes, all paid plans come with a 14-day free trial. No credit card required to start."
  },
  {
    question: "What payment methods do you accept?",
    answer: "We accept all major credit cards and debit cards through our secure Paystack payment gateway."
  },
  {
    question: "Can I cancel my subscription?",
    answer: "Yes, you can cancel your subscription anytime. You'll continue to have access until the end of your billing period."
  }
]

onMounted(() => {
  // Initialize Paystack inline if needed
  if (window.PaystackPop) {
    window.paystackSetup = {
      key: props.paystack_public_key,
    }
  }
})
</script>

<style scoped>
/* Custom styles can be added here */
</style>
