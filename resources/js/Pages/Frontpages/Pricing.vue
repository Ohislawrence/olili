<template>
    <AppLayout>
        <Head title="Pricing Plans - Olilearn" />
        <MetaTags
            title="Olilearn Pricing - Choose Your Perfect Learning Plan"
            description="Affordable pricing plans for our expert-curated courses and AI tutoring platform. Start with a free plan or upgrade for unlimited access and advanced features."
            image="/images/olingolearn.png"
        />

        <!-- Hero Section -->
        <section class="relative py-16 bg-gradient-to-br from-emerald-50 via-teal-50 to-white">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <Link :href="route('welcome')" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                Home
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="font-medium text-gray-700">Pricing</li>
                    </ol>
                </nav>

                <div class="text-center max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Simple, Transparent Pricing
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Choose Your
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            Learning Plan
                        </span>
                    </h1>

                    <p class="text-lg text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                        Start with free courses and upgrade for unlimited access to expert-curated content
                        and premium AI tutor features.
                    </p>

                    <!-- Key Benefits -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto">
                        <div class="flex items-center justify-center text-gray-600 p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Free Starter Plan
                        </div>
                        <div class="flex items-center justify-center text-gray-600 p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Cancel Anytime
                        </div>
                        <div class="flex items-center justify-center text-gray-600 p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            14-Day Free Trial
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Plans Component -->
        <section class="py-16 bg-gradient-to-b from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <PricingPlans
                    :plans="subscriptionPlans"
                    :show-role-selector="true"
                    :show-enterprise="true"
                    section-id="pricing-plans"
                    header-badge="Simple Pricing"
                    header-title="CHOOSE YOUR PLAN"
                    header-subtitle="FOR SUCCESS"
                    header-description="Select the perfect plan for your learning journey. All plans include access to our expert-curated courses and AI tutoring features."
                />
            </div>
        </section>

        <!-- Feature Comparison -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">
                        Plan Comparison
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 text-sm font-medium text-gray-700">Features</th>
                                    <th class="text-center py-4 text-sm font-medium text-gray-700">Free</th>
                                    <th class="text-center py-4 text-sm font-medium text-gray-700">Pro</th>
                                    <th class="text-center py-4 text-sm font-medium text-gray-700">Team</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="feature in features" :key="feature.name">
                                    <td class="py-4 text-sm text-gray-700">{{ feature.name }}</td>
                                    <td class="text-center py-4">
                                        <span v-if="feature.free === true" class="text-emerald-500">✓</span>
                                        <span v-else-if="typeof feature.free === 'string'" class="text-xs text-gray-600">{{ feature.free }}</span>
                                        <span v-else class="text-gray-300">✗</span>
                                    </td>
                                    <td class="text-center py-4">
                                        <span v-if="feature.pro === true" class="text-emerald-500">✓</span>
                                        <span v-else-if="typeof feature.pro === 'string'" class="text-xs text-gray-600">{{ feature.pro }}</span>
                                        <span v-else class="text-gray-300">✗</span>
                                    </td>
                                    <td class="text-center py-4">
                                        <span v-if="feature.team === true" class="text-emerald-500">✓</span>
                                        <span v-else-if="typeof feature.team === 'string'" class="text-xs text-gray-600">{{ feature.team }}</span>
                                        <span v-else class="text-gray-300">✗</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-16 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Frequently Asked Questions
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Common Questions About Our Plans
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Find answers to the most frequently asked questions about our pricing and plans.
                    </p>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="faq in faqs"
                        :key="faq.question"
                        class="bg-white rounded-xl p-6 border border-gray-200 hover:border-emerald-300 transition-colors cursor-pointer group"
                        @click="toggleFaq(faq)"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 group-hover:text-emerald-700 transition-colors">
                                {{ faq.question }}
                            </h3>
                            <svg
                                class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-transform duration-300"
                                :class="{ 'rotate-180': faq.open }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <transition
                            enter-active-class="transition-all duration-300 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-96"
                            leave-from-class="opacity-100 max-h-96"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div v-show="faq.open" class="mt-4 text-gray-600">
                                {{ faq.answer }}
                            </div>
                        </transition>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <Link
                        :href="route('faq')"
                        class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors group"
                    >
                        View all FAQs
                        <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-16 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="bg-white rounded-3xl p-8 md:p-12 border border-gray-200 shadow-lg">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl">
                        🎯
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Ready to Transform Your Learning?
                    </h2>

                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Start with our free plan today and experience expert-curated courses with AI tutoring.
                        Upgrade anytime to unlock premium features.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-bold hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Start Free Trial
                        </Link>
                        <Link
                            :href="route('courses.index')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all duration-300 border-2 border-gray-200 hover:border-gray-300"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Browse Free Courses
                        </Link>
                    </div>

                    <p class="text-sm text-gray-500 mt-6">
                        No credit card required • 14-day free trial • Cancel anytime
                    </p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/GuestLayout.vue'
import PricingPlans from '@/Components/PricingPlans.vue' // Added the import
import MetaTags from '@/Components/MetaTags.vue'
import { ref } from 'vue'

const props = defineProps({
    subscriptionPlans: Array
})

// Sample plans data for the PricingPlans component
const plans = ref([
    {
        name: 'Free',
        price: 0,
        description: 'Perfect for getting started',
        features: [
            'Access to free courses',
            'Basic AI tutor access',
            'Community support',
            'Limited course selection'
        ],
        cta: 'Get Started Free',
        ctaLink: route('register'),
        popular: false
    },
    {
        name: 'Pro',
        price: 3500,
        description: 'Best for serious learners',
        features: [
            'All courses unlocked',
            'Premium AI tutor access',
            'Downloadable certificates',
            'Priority email support',
            'Offline content access'
        ],
        cta: 'Start 14-Day Free Trial',
        ctaLink: route('register'),
        popular: true
    },
    {
        name: 'Team',
        price: 2500,
        description: 'For schools & institutions',
        features: [
            'Everything in Pro plus:',
            'Team management dashboard',
            'Progress tracking analytics',
            'Custom learning paths',
            'Dedicated account manager'
        ],
        cta: 'Contact for Pricing',
        ctaLink: route('contact'),
        popular: false
    }
])

const features = [
    { name: 'Access to Free Courses', free: 'Limited', pro: true, team: true },
    { name: 'Access to All Courses', free: false, pro: true, team: true },
    { name: 'AI Tutor Access', free: 'Basic', pro: 'Premium', team: 'Premium' },
    { name: 'Download Certificates', free: false, pro: true, team: true },
    { name: 'Offline Content Access', free: false, pro: true, team: true },
    { name: 'Priority Email Support', free: false, pro: true, team: true },
    { name: 'Progress Analytics', free: 'Basic', pro: 'Advanced', team: 'Advanced' },
    { name: 'Team Management', free: false, pro: false, team: true },
    { name: 'Dedicated Support', free: false, pro: false, team: true },
    { name: 'Custom Learning Paths', free: false, pro: false, team: true }
]

const faqs = ref([
    {
        question: "Can I try the Pro plan for free?",
        answer: "Yes! All new users get a 14-day free trial of our Pro plan. No credit card is required to start your trial. You'll get full access to all premium features during the trial period.",
        open: false
    },
    {
        question: "What payment methods do you accept?",
        answer: "We accept all major credit and debit cards, PayPal, and bank transfers (for Nigerian customers). All payments are processed securely through Paystack.",
        open: false
    },
    {
        question: "Can I switch between plans?",
        answer: "Yes, you can upgrade or downgrade your plan at any time. When you upgrade, you'll get immediate access to the new features. Downgrades take effect at the end of your current billing cycle.",
        open: false
    },
    {
        question: "Do you offer student discounts?",
        answer: "Yes, we offer special pricing for verified students. Please contact our support team with your student ID for more information about student discounts.",
        open: false
    },
    {
        question: "What happens if I cancel my subscription?",
        answer: "If you cancel, you'll continue to have access to premium features until the end of your current billing period. After that, your account will revert to the Free plan, and you'll lose access to premium features.",
        open: false
    },
    {
        question: "Can I get a refund?",
        answer: "We offer a 14-day money-back guarantee for all annual subscriptions. If you're not satisfied with our service within the first 14 days, contact our support team for a full refund.",
        open: false
    }
])

const toggleFaq = (faq) => {
    faq.open = !faq.open
}
</script>

<style scoped>
/* Animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

/* Pricing card hover effects */
.transform {
    transition: transform 0.3s ease;
}

.hover\:-translate-y-1:hover {
    transform: translateY(-0.25rem);
}

/* Table styling */
table {
    border-collapse: separate;
    border-spacing: 0;
}

th, td {
    padding: 1rem;
    text-align: left;
}

th:first-child, td:first-child {
    border-radius: 0.5rem 0 0 0.5rem;
}

th:last-child, td:last-child {
    border-radius: 0 0.5rem 0.5rem 0;
}

/* FAQ transition */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-1 > * {
        margin-bottom: 2rem;
    }

    .grid-cols-1 > *:last-child {
        margin-bottom: 0;
    }
}
</style>
