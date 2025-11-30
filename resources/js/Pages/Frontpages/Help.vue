<template>
    <AppLayout>
        <Head title="Help" />

        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-24 h-24 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/3 right-20 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/4 w-28 h-28 bg-white/10 rounded-full animate-bounce-slow"></div>
                <div class="absolute top-1/2 right-1/3 w-20 h-20 bg-emerald-400/20 rounded-full animate-ping-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-8 animate-fade-in">
                    <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                    Flexible Pricing Options
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight animate-fade-in-up">
                    Choose Your
                    <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                        Perfect Plan
                    </span>
                </h1>
                <p class="text-xl md:text-2xl mb-12 text-emerald-100 max-w-3xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                    Select the ideal plan for your learning journey. All plans include our core AI features and personalized learning tools.
                </p>

                <!-- Key Benefits -->
                <div class="flex flex-wrap justify-center gap-6 animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="flex items-center text-emerald-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        No hidden fees
                    </div>
                    <div class="flex items-center text-emerald-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Cancel anytime
                    </div>
                    <div class="flex items-center text-emerald-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        14-day free trial
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Plans Section -->
        <section class="py-24 bg-gradient-to-b from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <PricingPlans
                    :plans="subscriptionPlans"
                    :show-role-selector="true"
                    :show-enterprise="true"
                    section-id="pricing-plans"
                    header-badge="Simple Pricing"
                    header-title="AFFORDABLE"
                    header-subtitle="PLANS FOR EVERYONE"
                    header-description="Choose the perfect plan for your learning needs. All plans include our core AI features."
                />
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Frequently Asked Questions
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        HAVE QUESTIONS?
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">WE'VE GOT ANSWERS</span>
                    </h2>
                    <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                        Everything you need to know about our pricing and plans.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group"
                        :style="{ 'animation-delay': index * 0.1 + 's' }"
                    >
                        <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-emerald-700 transition-colors">
                            {{ faq.question }}
                        </h3>
                        <p class="text-gray-700 leading-relaxed">
                            {{ faq.answer }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

       
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/GuestLayout.vue'
import PricingPlans from '@/Components/PricingPlans.vue'
import { AcademicCapIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline'
import MetaTags from '@/Components/MetaTags.vue';

const props = defineProps({
    subscriptionPlans: Array
})

const faqs = [
    {
        question: "Can I change plans later?",
        answer: "Yes, you can upgrade or downgrade your plan at any time. Changes will be prorated accordingly and take effect immediately."
    },
    {
        question: "Is there a free trial?",
        answer: "All paid plans include a 14-day free trial. No credit card is required to start your trial period."
    },
    {
        question: "What payment methods do you accept?",
        answer: "We accept all major credit cards, PayPal, and bank transfers for enterprise plans. Payments are processed securely through Paystack."
    },
    {
        question: "Can I cancel anytime?",
        answer: "Yes, you can cancel your subscription at any time. You'll retain full access to premium features until the end of your current billing period."
    },
    {
        question: "Do you offer discounts for students?",
        answer: "Yes, we offer special educational discounts for students and academic institutions. Contact our support team for more information."
    },
    {
        question: "What happens after my trial ends?",
        answer: "After your trial ends, you'll automatically switch to our free plan unless you choose to subscribe to a paid plan during your trial period."
    },
    {
        question: "How often is new content added?",
        answer: "We continuously add new courses and learning materials. Our AI also generates personalized content based on your learning needs."
    },
    {
        question: "Can I access content offline?",
        answer: "Yes, premium subscribers can download course materials and content for offline access, perfect for learning without internet."
    }
]
</script>


