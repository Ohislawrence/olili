<template>
    <MetaTags
        title="Contact - Get in Touch with Your AI Learning Companion"
        description="Have questions about Olilearn? Contact our team in Nigeria. We're here to help students, parents, and tutors with AI-powered learning solutions."
        image="/images/olingolearn.png"
    />
    <AppLayout>
        <Head title="Contact Us - Olilearn Nigeria" />

        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/4 right-20 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-white/10 rounded-full animate-bounce-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-8">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        Get in Touch
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        We're Here to
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Help You Learn
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-emerald-100 max-w-3xl mx-auto">
                        Have questions about Olilearn? Our Nigerian team is ready to assist students, parents,
                        and tutors with our AI-powered learning platform.
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <!-- Contact Form -->
                    <div>
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Send us a Message
                        </div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-8">
                            Get in Touch
                            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                with Our Team
                            </span>
                        </h2>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        First Name *
                                    </label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        v-model="form.first_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="Enter your first name"
                                    >
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Name *
                                    </label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        v-model="form.last_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="Enter your last name"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    v-model="form.email"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    placeholder="your.email@example.com"
                                >
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number (Nigeria)
                                </label>
                                <input
                                    type="tel"
                                    id="phone"
                                    v-model="form.phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    placeholder="+234 800 000 0000"
                                >
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject *
                                </label>
                                <select
                                    id="subject"
                                    v-model="form.subject"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                >
                                    <option value="" disabled selected>Select a topic</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="student">Student Support</option>
                                    <option value="parent">Parent Inquiry</option>
                                    <option value="tutor">Tutor Partnership</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="billing">Billing & Payment</option>
                                    <option value="business">Business Partnership</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Your Message *
                                </label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="6"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    placeholder="How can we help you with AI-powered learning?"
                                ></textarea>
                            </div>

                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="newsletter"
                                    v-model="form.subscribe_newsletter"
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                >
                                <label for="newsletter" class="ml-3 text-sm text-gray-700">
                                    Subscribe to our newsletter for learning tips and updates
                                </label>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    :disabled="submitting"
                                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="submitting" class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Sending Message...
                                    </span>
                                    <span v-else class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Send Message
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <!-- Contact Card -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-8 border border-emerald-100">
                            <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                                Contact Details
                            </div>

                            <div class="space-y-6">
                                <!-- Email -->
                                <div class="flex items-start">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Email Us</h3>
                                        <a :href="`mailto:${contactEmail}`" class="text-emerald-700 hover:text-emerald-800 transition-colors">
                                            {{ contactEmail }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">Typically respond within 24 hours</p>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-start">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Call Us (Nigeria)</h3>
                                        <a href="tel:+2348000000000" class="text-emerald-700 hover:text-emerald-800 transition-colors">
                                            +234 800 000 0000
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">Mon - Fri, 9AM - 6PM WAT</p>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-start">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Our Office</h3>
                                        <p class="text-gray-700">Lagos, Nigeria</p>
                                        <p class="text-sm text-gray-600 mt-1">Serving learners across Nigeria</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQs -->
                        <div class="bg-white rounded-2xl p-8 border border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h3>

                            <div class="space-y-4">
                                <div v-for="faq in faqs" :key="faq.question" class="border-b border-gray-100 pb-4">
                                    <button
                                        @click="toggleFaq(faq)"
                                        class="w-full flex items-center justify-between text-left"
                                    >
                                        <span class="text-lg font-medium text-gray-900">{{ faq.question }}</span>
                                        <svg
                                            class="w-5 h-5 text-emerald-600 transition-transform duration-300"
                                            :class="{ 'rotate-180': faq.open }"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <transition
                                        enter-active-class="transition-all duration-300 ease-out"
                                        leave-active-class="transition-all duration-300 ease-in"
                                        enter-from-class="opacity-0 max-h-0"
                                        enter-to-class="opacity-100 max-h-96"
                                        leave-from-class="opacity-100 max-h-96"
                                        leave-to-class="opacity-0 max-h-0"
                                    >
                                        <div v-show="faq.open" class="mt-3 text-gray-700">
                                            {{ faq.answer }}
                                        </div>
                                    </transition>
                                </div>
                            </div>

                            <div class="mt-8">
                                <Link
                                    :href="route('faq')"
                                    class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-semibold transition-colors group"
                                >
                                    View all FAQs
                                    <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Support Section -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        OTHER WAYS TO
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            GET SUPPORT
                        </span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Quick access to help and resources for your learning journey
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Help Center -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            📚
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Help Center</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Browse our comprehensive guides and tutorials for students, parents, and tutors
                        </p>
                        <Link
                            :href="route('help')"
                            class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-semibold transition-colors group"
                        >
                            Visit Help Center
                            <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Community -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            👥
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Community Forum</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Connect with other learners, share experiences, and get peer support
                        </p>
                        <Link
                            :href="route('community.index')"
                            class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-semibold transition-colors group"
                        >
                            Join Community
                            <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Live Chat -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            💬
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Live Chat</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Chat with our support team during business hours for quick assistance
                        </p>
                        <button
                            @click="startLiveChat"
                            class="inline-flex items-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300 transform hover:scale-105"
                        >
                            Start Live Chat
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Response Time -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        OUR COMMITMENT TO
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            QUALITY SUPPORT
                        </span>
                    </h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">24h</div>
                        <div class="text-emerald-600 font-medium">Email Response</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">98%</div>
                        <div class="text-emerald-600 font-medium">Satisfaction Rate</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">7</div>
                        <div class="text-emerald-600 font-medium">Days a Week</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">NG</div>
                        <div class="text-emerald-600 font-medium">Nigerian Team</div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const contactEmail = 'hello@olilearn.com';
const submitting = ref(false);
const messageSent = ref(false);

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
    subscribe_newsletter: true
});

const faqs = ref([
    {
        question: 'Is Olilearn available for Nigerian students?',
        answer: 'Yes! Olilearn is specifically designed to support Nigerian students with localized content and AI tutors that understand our educational curriculum. Our team is based in Lagos and we optimize for Nigerian internet speeds.',
        open: false
    },
    {
        question: 'How much does Olilearn cost?',
        answer: 'We offer both free and premium plans. You can start learning for free with basic features, and upgrade to premium for advanced AI tutoring and personalized learning paths. Visit our pricing page for detailed information.',
        open: false
    },
    {
        question: 'Can parents monitor their children\'s progress?',
        answer: 'Absolutely! Parents get access to a dedicated dashboard where they can track their child\'s learning progress, view completed lessons, and receive regular progress reports via email.',
        open: false
    },
    {
        question: 'Do you support Nigerian payment methods?',
        answer: 'Yes, we accept all major Nigerian payment methods including bank transfers, debit cards, and mobile money. We also offer special educational rates for schools and institutions.',
        open: false
    }
]);

const submitForm = async () => {
    submitting.value = true;
    
    try {
        const response = await axios.post('/contact/submit', form);
        
        if (response.data.success) {
            // Show success message
            alert(response.data.message);
            
            // Reset form
            Object.assign(form, {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                subject: '',
                message: '',
                subscribe_newsletter: true
            });
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            // Handle validation errors
            console.error('Validation errors:', error.response.data.errors);
            alert('Please check the form for errors.');
        } else {
            console.error('Error submitting form:', error);
            alert('There was an error sending your message. Please try again later or email us directly at hello@olilearn.com');
        }
    } finally {
        submitting.value = false;
    }
};

const toggleFaq = (faq) => {
    faq.open = !faq.open;
};

const startLiveChat = () => {
    // In a real app, this would trigger your live chat widget
    alert('Live chat feature coming soon! For now, please email us at hello@olilearn.com');
};
</script>

<style scoped>
/* Reuse animations from other pages */
@keyframes pulse-slow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite;
}

/* Form focus styles */
input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Custom scrollbar for textarea */
textarea {
    resize: vertical;
    min-height: 120px;
}

/* Transition for FAQ dropdown */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Card hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}
</style>
