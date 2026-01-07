<template>
    <MetaTags
        title="Contact Olilearn - Support for Students & Educators"
        description="Get in touch with our team for support with our expert-curated courses and AI tutoring platform. We're here to help you succeed in your learning journey."
        image="/images/olingolearn.png"
    />
    <AppLayout>
        <Head title="Contact Us - Olilearn Support" />

        <!-- Hero Section - Lighter Green -->
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
                        <li class="font-medium text-gray-700">Contact</li>
                    </ol>
                </nav>

                <div class="text-center">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        We're Here to Help
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Get Support for Your
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            Learning Journey
                        </span>
                    </h1>

                    <p class="text-lg text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Have questions about our expert-curated courses or need help with your AI tutor?
                        Our support team is ready to assist students and educators.
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Contact Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Contact Information -->
                    <div class="lg:col-span-1 space-y-8">
                        <!-- Contact Card -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Contact Information</h3>
                            </div>

                            <div class="space-y-6">
                                <!-- Email -->
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-1">Email Support</h4>
                                        <a :href="`mailto:${contactEmail}`" class="text-emerald-600 hover:text-emerald-700 transition-colors block">
                                            {{ contactEmail }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">Typically respond within 24 hours</p>
                                    </div>
                                </div>

                                <!-- Student Support -->
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" transform="translate(0 7)"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-1">Student Support</h4>
                                        <a href="mailto:students@olilearn.com" class="text-blue-600 hover:text-blue-700 transition-colors block">
                                            students@olilearn.com
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">Course enrollment & AI tutor help</p>
                                    </div>
                                </div>

                                <!-- Business Inquiries -->
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-1">Business Inquiries</h4>
                                        <a href="mailto:partnerships@olilearn.com" class="text-purple-600 hover:text-purple-700 transition-colors block">
                                            partnerships@olilearn.com
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">For schools & institutions</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Office Hours -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Office Hours</h3>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-700">Monday - Friday</span>
                                    <span class="font-medium text-gray-900">9:00 AM - 6:00 PM WAT</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-700">Saturday</span>
                                    <span class="font-medium text-gray-900">10:00 AM - 4:00 PM WAT</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-700">Sunday</span>
                                    <span class="font-medium text-gray-900">Email Support Only</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-900">Note:</span> AI Tutor support is available 24/7 within the platform.
                                </p>
                            </div>
                        </div>

                        <!-- Quick Help -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Help Resources</h3>
                            <div class="space-y-3">
                                <Link
                                    :href="route('help')"
                                    class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow-sm transition-all group"
                                >
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 group-hover:text-emerald-700">Help Center</div>
                                        <div class="text-sm text-gray-500">Guides & tutorials</div>
                                    </div>
                                </Link>
                                <Link
                                    :href="route('faq')"
                                    class="flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow-sm transition-all group"
                                >
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 group-hover:text-emerald-700">FAQ</div>
                                        <div class="text-sm text-gray-500">Common questions answered</div>
                                    </div>
                                </Link>
                                <button
                                    @click="startLiveChat"
                                    class="w-full flex items-center p-3 bg-white rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow-sm transition-all group"
                                >
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-900 group-hover:text-emerald-700">Live Chat</div>
                                        <div class="text-sm text-gray-500">Available during office hours</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-200 shadow-sm">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">Send us a Message</h2>
                                <p class="text-gray-600">
                                    Fill out the form below and our team will get back to you as soon as possible.
                                </p>
                            </div>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- Name Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            First Name *
                                        </label>
                                        <input
                                            type="text"
                                            id="first_name"
                                            v-model="form.first_name"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50"
                                            placeholder="John"
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
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50"
                                            placeholder="Doe"
                                        >
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email Address *
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            v-model="form.email"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50"
                                            placeholder="john@example.com"
                                        >
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number (Optional)
                                        </label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            v-model="form.phone"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50"
                                            placeholder="+234 800 000 0000"
                                        >
                                    </div>
                                </div>

                                <!-- Inquiry Type -->
                                <div>
                                    <label for="inquiry_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        What can we help you with? *
                                    </label>
                                    <select
                                        id="inquiry_type"
                                        v-model="form.inquiry_type"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50 appearance-none"
                                    >
                                        <option value="" disabled selected>Select an inquiry type</option>
                                        <option value="course_enrollment">Course Enrollment</option>
                                        <option value="ai_tutor_support">AI Tutor Support</option>
                                        <option value="technical_issue">Technical Issue</option>
                                        <option value="account_billing">Account & Billing</option>
                                        <option value="content_feedback">Course Content Feedback</option>
                                        <option value="partnership">Partnership Inquiry</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                        Subject *
                                    </label>
                                    <input
                                        type="text"
                                        id="subject"
                                        v-model="form.subject"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50"
                                        placeholder="Brief summary of your inquiry"
                                    >
                                </div>

                                <!-- Message -->
                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                        Your Message *
                                    </label>
                                    <textarea
                                        id="message"
                                        v-model="form.message"
                                        rows="6"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all duration-300 bg-gray-50 resize-none"
                                        placeholder="Please provide as much detail as possible about your inquiry..."
                                    ></textarea>
                                </div>

                                <!-- Attachment -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Attachments (Optional)
                                    </label>
                                    <div class="flex items-center justify-center w-full">
                                        <label for="attachment" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500">
                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 10MB</p>
                                            </div>
                                            <input id="attachment" type="file" class="hidden" @change="handleFileUpload" />
                                        </label>
                                    </div>
                                </div>

                                <!-- Newsletter -->
                                <div class="flex items-start">
                                    <input
                                        type="checkbox"
                                        id="newsletter"
                                        v-model="form.subscribe_newsletter"
                                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1"
                                    >
                                    <label for="newsletter" class="ml-3 text-sm text-gray-700">
                                        Subscribe to receive learning tips, course updates, and platform announcements.
                                        You can unsubscribe at any time.
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button
                                        type="submit"
                                        :disabled="submitting"
                                        :class="[
                                            'w-full px-6 py-4 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center',
                                            submitting
                                                ? 'bg-emerald-400 cursor-not-allowed'
                                                : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white hover:shadow-lg hover:-translate-y-0.5'
                                        ]"
                                    >
                                        <svg v-if="submitting" class="animate-spin h-5 w-5 mr-3 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        {{ submitting ? 'Sending Message...' : 'Send Message' }}
                                    </button>

                                    <p class="text-xs text-gray-500 text-center mt-3">
                                        By submitting this form, you agree to our
                                        <Link :href="route('privacy')" class="text-emerald-600 hover:text-emerald-700">Privacy Policy</Link>.
                                    </p>
                                </div>

                                <!-- Success Message -->
                                <div v-if="messageSent" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm animate-fade-in">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Your message has been sent successfully! We'll get back to you within 24 hours.
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ errorMessage }}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Support Channels -->
        <section class="py-16 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Additional Support Channels
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Connect with our community and access helpful resources
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Community Forum -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl mb-4">
                            👥
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Community Forum</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Connect with fellow learners, share experiences, and get peer support in our active community.
                        </p>
                        <Link
                            :href="route('community.index')"
                            class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm"
                        >
                            Join the Discussion
                            <svg class="ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Status Page -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 text-xl mb-4">
                            📊
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">System Status</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Check our platform status and get real-time updates on any ongoing maintenance or issues.
                        </p>
                        <a
                            href="/status"
                            class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm"
                        >
                            View Status Page
                            <svg class="ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Documentation -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 text-xl mb-4">
                            📖
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Documentation</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Access comprehensive guides, API documentation, and developer resources.
                        </p>
                        <a
                            href="/docs"
                            class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium text-sm"
                        >
                            Read Documentation
                            <svg class="ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Response Commitment -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Our Commitment to You
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        We're Dedicated to Your Success
                    </h2>

                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Our support team is committed to helping you make the most of our expert-curated
                        courses and AI tutoring platform. We strive to provide timely, helpful responses
                        to all inquiries.
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                        <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="text-2xl font-bold text-emerald-700">24h</div>
                            <div class="text-sm text-gray-600">Response Time</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="text-2xl font-bold text-emerald-700">95%</div>
                            <div class="text-sm text-gray-600">Satisfaction Rate</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="text-2xl font-bold text-emerald-700">7</div>
                            <div class="text-sm text-gray-600">Days Support</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="text-2xl font-bold text-emerald-700">100+</div>
                            <div class="text-sm text-gray-600">Courses Supported</div>
                        </div>
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
const errorMessage = ref('');

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    inquiry_type: '',
    subject: '',
    message: '',
    subscribe_newsletter: true,
    attachment: null
});

const submitForm = async () => {
    submitting.value = true;
    errorMessage.value = '';

    try {
        // Create FormData for file upload
        const formData = new FormData();
        Object.keys(form).forEach(key => {
            if (key === 'attachment' && form.attachment) {
                formData.append('attachment', form.attachment);
            } else {
                formData.append(key, form[key]);
            }
        });

        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500));

        // Success response
        messageSent.value = true;

        // Reset form
        Object.assign(form, {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            inquiry_type: '',
            subject: '',
            message: '',
            subscribe_newsletter: true,
            attachment: null
        });

        // Clear success message after 5 seconds
        setTimeout(() => {
            messageSent.value = false;
        }, 5000);

    } catch (error) {
        console.error('Error submitting form:', error);
        errorMessage.value = 'There was an error sending your message. Please try again later or email us directly at hello@olilearn.com';

        // Clear error message after 5 seconds
        setTimeout(() => {
            errorMessage.value = '';
        }, 5000);
    } finally {
        submitting.value = false;
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            errorMessage.value = 'File size must be less than 10MB';
            setTimeout(() => {
                errorMessage.value = '';
            }, 5000);
            return;
        }

        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            errorMessage.value = 'Only JPG, PNG, and PDF files are allowed';
            setTimeout(() => {
                errorMessage.value = '';
            }, 5000);
            return;
        }

        form.attachment = file;
    }
};

const startLiveChat = () => {
    // In a real app, this would trigger your live chat widget
    alert('Live chat support is available during office hours. For now, please use the contact form above or email us at hello@olilearn.com');
};
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

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Form styling */
input:focus, textarea:focus, select:focus {
    outline: none;
}

/* Custom select arrow */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

/* File upload hover */
#attachment:hover {
    background-color: #f9fafb;
}

/* Button hover effect */
button:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Card hover effects */
.bg-white:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
