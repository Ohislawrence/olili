<template>
    <section class="py-16 bg-gradient-to-br from-slate-50 to-emerald-50" :id="sectionId">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                    {{ headerBadge }}
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ headerTitle }}
                    <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                        {{ headerSubtitle }}
                    </span>
                </h2>
                <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                    {{ headerDescription }}
                </p>

                <!-- Role Selector -->
                <div v-if="showRoleSelector" class="flex flex-wrap justify-center gap-4 mt-8">
                    <button
                        v-for="role in availableRoles"
                        :key="role.value"
                        @click="selectedRole = role.value"
                        class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-sm hover:shadow-md"
                        :class="selectedRole === role.value
                            ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg'
                            : 'bg-white text-gray-700 border border-gray-300 hover:border-emerald-300'"
                    >
                        {{ role.label }}
                    </button>
                </div>

                <!-- Billing Toggle -->
                <div class="flex items-center justify-center mt-8">
                    <span class="mr-4 text-gray-700 font-medium">Monthly</span>
                    <button
                        @click="billingCycle = billingCycle === 'monthly' ? 'yearly' : 'monthly'"
                        class="relative inline-flex h-6 w-12 items-center rounded-full bg-gray-300 transition-colors duration-300 shadow-inner"
                        :class="billingCycle === 'yearly' ? 'bg-emerald-500' : 'bg-gray-300'"
                    >
                        <span class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform duration-300 shadow-lg"
                            :class="billingCycle === 'yearly' ? 'translate-x-7' : 'translate-x-1'">
                        </span>
                    </button>
                    <span class="ml-4 text-gray-700 font-medium">Yearly</span>
                    <span class="ml-2 px-2.5 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200">
                        Save 10%
                    </span>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-flex items-center px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-200">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading plans...
                </div>
            </div>

            <!-- Plans Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    v-for="plan in filteredPlans"
                    :key="plan.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-500 pricing-card group relative"
                    :class="{
                        'ring-2 ring-emerald-500 transform scale-105 relative z-10': plan.is_popular,
                        'border border-gray-200': !plan.is_popular
                    }"
                >
                    <!-- Popular Badge -->
                    <div v-if="plan.is_popular" class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                            Most Popular
                        </span>
                    </div>

                    <div class="p-8">
                        <!-- Plan Header -->
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plan.name }}</h3>
                            <p class="text-gray-600 text-sm">{{ plan.description }}</p>
                        </div>

                        <!-- Price -->
                        <div class="text-center mb-6">
                            <div class="flex items-baseline justify-center">
                                <span class="text-4xl font-bold text-gray-900">
                                    {{ formatPrice(billingCycle === 'monthly' ? plan.monthly_price : plan.yearly_price) }}
                                </span>
                                <span class="text-gray-600 ml-2">
                                    /{{ billingCycle === 'monthly' ? 'month' : 'year' }}
                                </span>
                            </div>
                            <p v-if="billingCycle === 'yearly' && !plan.is_free" class="text-emerald-600 text-sm font-semibold mt-1">
                                Save {{ calculateSavings(plan) }} per year
                            </p>
                            <p v-if="plan.is_free" class="text-gray-500 text-sm mt-1">
                                Forever free
                            </p>
                        </div>

                        <!-- Features -->
                        <ul class="space-y-4 mb-8">
                            <li
                                v-for="feature in getPlanFeatures(plan)"
                                :key="feature"
                                class="flex items-start text-gray-700"
                            >
                                <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span
                                    class="text-sm cursor-pointer"
                                    @click="feature === '__see_more__' ? openFeatureModal(plan) : null"
                                >
                                    {{ feature === '__see_more__' ? 'See more' : feature }}
                                </span>
                            </li>
                        </ul>

                        <!-- Limits -->
                        <div v-if="hasLimits(plan)" class="mb-6 p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                            <h4 class="font-semibold text-gray-900 text-sm mb-2">Plan Limits</h4>
                            <div class="space-y-2 text-xs text-gray-600">
                                <div v-if="plan.max_courses" class="flex justify-between">
                                    <span>Max Courses:</span>
                                    <span class="font-medium">{{ plan.max_courses === 999999 ? 'Unlimited' : plan.max_courses }}</span>
                                </div>
                                <div v-if="plan.max_ai_requests_per_month" class="flex justify-between">
                                    <span>AI Requests/Month:</span>
                                    <span class="font-medium">{{ plan.max_ai_requests_per_month === 999999 ? 'Unlimited' : plan.max_ai_requests_per_month.toLocaleString() }}</span>
                                </div>
                                <div v-if="plan.ai_grading" class="flex justify-between">
                                    <span>AI Grading:</span>
                                    <span class="font-medium text-emerald-600">Included</span>
                                </div>
                                <div v-if="plan.priority_support" class="flex justify-between">
                                    <span>Priority Support:</span>
                                    <span class="font-medium text-emerald-600">Yes</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <Link
                            :href="plan.is_free ? route('register') : route('register', { plan: plan.code })"
                            class="w-full block text-center py-4 px-6 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 shadow-sm hover:shadow-md"
                            :class="getButtonClasses(plan)"
                        >
                            {{ getButtonText(plan) }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && filteredPlans.length === 0" class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Plans Available</h3>
                <p class="text-gray-500">Please check back later for available subscription plans.</p>
            </div>

            <!-- Enterprise CTA -->
            <div v-if="showEnterprise" class="mt-16 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-sm border border-emerald-200 p-8 text-center">
                <div class="max-w-2xl mx-auto">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Need Custom Solutions?</h3>
                    <p class="text-xl text-gray-700 mb-6">
                        We offer enterprise plans for schools, universities, and organizations with custom requirements.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            :href="route('contact')"
                            class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            Contact Sales
                        </Link>
                        <Link
                            :href="route('enterprise')"
                            class="border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold hover:bg-emerald-50 transform hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            Learn More
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div
    v-if="showFeatureModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative">

        <!-- Close button -->
        <button
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
            @click="showFeatureModal = false"
        >
            ✕
        </button>

        <h3 class="text-2xl font-bold mb-4 text-gray-900">
            {{ modalPlanName }} – All Features
        </h3>

        <ul class="space-y-3 max-h-96 overflow-y-auto pr-2">
            <li
                v-for="f in modalFeatures"
                :key="f"
                class="flex items-start text-gray-700"
            >
                <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ f }}</span>
            </li>
        </ul>

        <div class="text-right mt-6">
            <button
                @click="showFeatureModal = false"
                class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition"
            >
                Close
            </button>
        </div>
    </div>
</div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';


const showFeatureModal = ref(false);
const modalFeatures = ref([]);
const modalPlanName = ref('');


const props = defineProps({
    plans: {
        type: Array,
        default: () => []
    },
    role: {
        type: String,
        default: 'student'
    },
    showRoleSelector: {
        type: Boolean,
        default: true
    },
    showEnterprise: {
        type: Boolean,
        default: true
    },
    sectionId: {
        type: String,
        default: 'pricing'
    },
    headerBadge: {
        type: String,
        default: 'Simple Pricing'
    },
    headerTitle: {
        type: String,
        default: 'AFFORDABLE'
    },
    headerSubtitle: {
        type: String,
        default: 'PLANS FOR EVERYONE'
    },
    headerDescription: {
        type: String,
        default: 'Choose the perfect plan for your learning needs. All plans include our core AI features.'
    }
});

// Reactive state
const billingCycle = ref('monthly');
const selectedRole = ref(props.role);
const loading = ref(false);

// Available roles
const availableRoles = [
    { value: 'student', label: 'For Students' },
    { value: 'tutor', label: 'For Tutors' },
    { value: 'organization', label: 'For Organizations' }
];

// Computed properties
const filteredPlans = computed(() => {
    return props.plans
        .filter(plan => plan.role === selectedRole.value && plan.is_active)
        .sort((a, b) => a.sort_order - b.sort_order);
});


// Price formatting
const formatPrice = (price) => {
    if (price === 0) return 'Free';
    return `₦${new Intl.NumberFormat('en-NG').format(parseFloat(price))}`;
};

const calculateSavings = (plan) => {
    const yearlySavings = (plan.monthly_price * 12) - plan.yearly_price;
    return `₦${yearlySavings.toFixed(0)}`;
};

// 🔥 Convert "save_progress_history" → "Save Progress History"
const formatFeature = (feature) => {
    return feature
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

// 🔥 Limit to 5 features + "See more"
const getPlanFeatures = (plan) => {
    const raw = plan.features && plan.features.length > 0
        ? plan.features
        : (plan.recommended_features || []);

    const formatted = raw.map(f => formatFeature(f));

    if (formatted.length > 5) {
        return [...formatted.slice(0, 5), '__see_more__'];
    }

    return formatted;
};

const hasLimits = (plan) => {
    return plan.max_courses || plan.max_ai_requests_per_month || plan.ai_grading || plan.priority_support;
};

const getButtonClasses = (plan) => {
    if (plan.is_popular) {
        return 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:shadow-lg hover:from-emerald-500 hover:to-teal-500 shadow-sm hover:shadow-md';
    } else if (plan.is_free) {
        return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-900 hover:from-gray-200 hover:to-gray-300 border border-gray-300 shadow-sm hover:shadow-md';
    } else {
        return 'bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 hover:from-emerald-200 hover:to-teal-200 border border-emerald-300 shadow-sm hover:shadow-md';
    }
};

const getButtonText = (plan) => {
    if (plan.is_free) {
        return 'Get Started Free';
    } else {
        return `Choose ${plan.name}`;
    }
};

// Watch for role changes
watch(selectedRole, () => {});

const openFeatureModal = (plan) => {
    modalPlanName.value = plan.name;
    modalFeatures.value = (plan.features && plan.features.length > 0)
        ? plan.features.map(f => formatFeature(f))
        : (plan.recommended_features || []).map(f => formatFeature(f));

    showFeatureModal.value = true;
};
</script>

<style scoped>
.pricing-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.pricing-card:hover {
    transform: translateY(-8px);
}

/* Ensure popular plan stays on top */
.pricing-card.ring-2 {
    z-index: 10;
}
</style>
