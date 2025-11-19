<!-- resources/js/Pages/frontpages/Pricing.vue -->
<template>
    <AppLayout>
        <!-- Header -->
        <section class="relative py-20 bg-gradient-to-br from-purple-600 to-pink-600 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Pricing Plans</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Choose the perfect plan for your learning journey. All plans include our core AI features.
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-purple-100">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        No hidden fees
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Cancel anytime
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        14-day free trial
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Plans Component -->
        <PricingPlans
            :plans="subscriptionPlans"
            :show-role-selector="true"
            :show-enterprise="true"
            section-id="pricing-plans"
            header-badge="Flexible Plans"
            header-title="CHOOSE YOUR"
            header-subtitle="PERFECT PLAN"
            header-description="Select from our range of plans designed for students, tutors, and organizations."
        />

        <!-- FAQ Section -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-xl text-gray-600">Everything you need to know about our pricing and plans.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div v-for="faq in faqs" :key="faq.question" class="bg-gray-50 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ faq.question }}</h3>
                        <p class="text-gray-700">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-16 bg-gradient-to-br from-purple-600 to-pink-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Get Started?</h2>
                <p class="text-xl text-purple-100 mb-8">
                    Join thousands of learners and educators who are already using Olilearn.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="bg-white text-purple-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300"
                    >
                        Start Free Trial
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transform hover:scale-105 transition-all duration-300"
                    >
                        Contact Sales
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>

import { Link,Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import PricingPlans from '@/Components/PricingPlans.vue';

const props = defineProps({
    subscriptionPlans: Array
});

const faqs = [
    {
        question: "Can I change plans later?",
        answer: "Yes, you can upgrade or downgrade your plan at any time. Changes will be prorated accordingly."
    },
    {
        question: "Is there a free trial?",
        answer: "All paid plans include a 14-day free trial. No credit card required to start."
    },
    {
        question: "What payment methods do you accept?",
        answer: "We accept all major credit cards, PayPal, and bank transfers for enterprise plans."
    },
    {
        question: "Can I cancel anytime?",
        answer: "Yes, you can cancel your subscription at any time. You'll retain access until the end of your billing period."
    },
    {
        question: "Do you offer discounts for students?",
        answer: "Yes, we offer special educational discounts. Contact our support team for more information."
    },
    {
        question: "What happens after my trial ends?",
        answer: "After your trial, you'll be automatically switched to the free plan unless you choose a paid plan."
    }
];
</script>
