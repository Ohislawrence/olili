<template>
    <MetaTags
        title="Enterprise Solutions - Olilearn Corporate Partnerships"
        description="Partner with Olilearn for Corporate Social Responsibility (CSR), corporate volunteerism, and corporate giving programs. Sponsor Nigerian students' education through AI-powered learning."
        image="/images/olingolearn.png"
    />
    <AppLayout>
        <Head title="Enterprise Partnerships - Olilearn" />

        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-emerald-900 to-teal-800 text-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-24 h-24 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/4 right-20 w-40 h-40 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/3 w-32 h-32 bg-white/10 rounded-full animate-bounce-slow"></div>
                <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-emerald-400/20 rounded-full animate-ping-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-6 py-3 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-lg font-medium mb-8">
                        <span class="w-3 h-3 bg-white rounded-full mr-2 animate-pulse"></span>
                        Corporate Partnerships
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
                        Transform Education Through
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Corporate Partnership
                        </span>
                    </h1>
                    <p class="text-2xl md:text-3xl mb-12 text-emerald-100 max-w-4xl mx-auto leading-relaxed">
                        Partner with Olilearn to empower Nigerian students through CSR, volunteerism,
                        and strategic giving programs powered by AI education.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center">
                        <button
                            @click="scrollToForm"
                            class="group bg-white text-emerald-800 px-10 py-5 rounded-xl font-bold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:bg-emerald-50"
                        >
                            <span class="relative flex items-center">
                                Partner with Us
                                <svg class="ml-3 w-6 h-6 transform transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </span>
                        </button>
                        <Link
                            :href="route('contact')"
                            class="group bg-transparent border-2 border-white text-white px-10 py-5 rounded-xl font-bold text-lg transform transition-all duration-300 hover:scale-105 hover:bg-white/10"
                        >
                            <span class="relative flex items-center">
                                Schedule a Demo
                                <svg class="ml-3 w-6 h-6 transform transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Impact Stats -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-8 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <div class="text-4xl md:text-5xl font-bold text-emerald-800 mb-2">5,000+</div>
                        <div class="text-emerald-700 font-semibold text-lg">Students Sponsored</div>
                    </div>
                    <div class="p-8 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <div class="text-4xl md:text-5xl font-bold text-emerald-800 mb-2">150+</div>
                        <div class="text-emerald-700 font-semibold text-lg">Corporate Partners</div>
                    </div>
                    <div class="p-8 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <div class="text-4xl md:text-5xl font-bold text-emerald-800 mb-2">₦500M+</div>
                        <div class="text-emerald-700 font-semibold text-lg">Educational Impact</div>
                    </div>
                    <div class="p-8 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <div class="text-4xl md:text-5xl font-bold text-emerald-800 mb-2">98%</div>
                        <div class="text-emerald-700 font-semibold text-lg">Partner Satisfaction</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partnership Models Section -->
        <section class="py-24 bg-gradient-to-b from-white to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-6 py-3 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 text-lg font-medium mb-6">
                        <span class="w-3 h-3 bg-emerald-600 rounded-full mr-2"></span>
                        Our Partnership Models
                    </div>
                    <h2 class="text-5xl font-bold text-gray-900 mb-6">
                        Strategic
                        <span class="block text-emerald-800 bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">
                            Corporate Giving Programs
                        </span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-3xl mx-auto">
                        Choose from flexible partnership models designed to maximize your CSR impact
                        while empowering Nigerian students with AI-powered education.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <!-- CSR Sponsorship -->
                    <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 border-2 border-emerald-100 transform hover:-translate-y-2">
                        <div class="w-20 h-20 mx-auto mb-8 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl">
                            🤝
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">CSR Sponsorship Program</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Sponsor students from underserved communities</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Custom branded learning programs</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Detailed impact reports and analytics</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Tax-deductible contributions in Nigeria</span>
                            </div>
                        </div>
                        
                    </div>

                    <!-- Corporate Volunteerism -->
                    <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 border-2 border-emerald-200 transform hover:-translate-y-2 relative">
                        <div class="absolute top-0 right-0 bg-emerald-600 text-white px-6 py-2 rounded-bl-2xl rounded-tr-2xl text-sm font-semibold">
                            Most Popular
                        </div>
                        <div class="w-20 h-20 mx-auto mb-8 rounded-2xl bg-gradient-to-br from-emerald-700 to-teal-700 flex items-center justify-center text-white text-3xl">
                            👥
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Corporate Volunteer Program</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Employee mentoring & tutoring opportunities</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Team-based learning projects</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Skills-based volunteering opportunities</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Employee engagement tracking</span>
                            </div>
                        </div>
                    </div>

                    <!-- Corporate Giving -->
                    <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 border-2 border-emerald-100 transform hover:-translate-y-2">
                        <div class="w-20 h-20 mx-auto mb-8 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl">
                            💝
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Corporate Giving Platform</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">One-time or recurring donations</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Match employee donations 2:1 or 3:1</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Grants for educational initiatives</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700">Transparent impact tracking</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Partnership Benefits -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-12 text-white">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h3 class="text-3xl font-bold mb-6">Why Partner With Olilearn?</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-200 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-lg">Measurable social impact aligned with UN SDG 4: Quality Education</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-200 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-lg">Enhanced brand reputation and employee engagement</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-200 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-lg">Tax benefits under Nigerian Corporate Affairs Commission guidelines</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-200 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-lg">Custom reports and regular impact assessments</span>
                                </li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <div class="text-6xl mb-4">🎯</div>
                            <p class="text-xl text-emerald-100">
                                "Together, we can bridge the educational divide and create lasting change
                                in Nigeria's educational landscape."
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Stories -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-6 py-3 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 text-lg font-medium mb-6">
                        <span class="w-3 h-3 bg-emerald-600 rounded-full mr-2"></span>
                        Success Stories
                    </div>
                    <h2 class="text-5xl font-bold text-gray-900 mb-6">
                        Corporate Partners
                        <span class="block text-emerald-800 bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">
                            Making a Difference
                        </span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-3xl mx-auto">
                        See how leading Nigerian and international companies are transforming education
                        through strategic partnerships with Olilearn.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center text-emerald-600 text-2xl mr-4">
                                🏦
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">Testoria NG</h4>
                                <p class="text-emerald-700 font-semibold">CSR Partnership since 2023</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic mb-6">
                            "Through our partnership with Olilearn, we've sponsored 200 underprivileged students
                            across Nigeria. The AI-powered platform allows us to track each student's progress
                            and ensure our CSR investment creates real impact."
                        </p>
                        <div class="pt-6 border-t border-emerald-100">
                            <div class="flex justify-between">
                                <span class="text-emerald-800 font-bold">200 Students</span>
                                <span class="text-gray-600">Sponsored</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center text-emerald-600 text-2xl mr-4">
                                ⚡
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">EverGreen Stores</h4>
                                <p class="text-emerald-700 font-semibold">Employee Volunteer Program</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic mb-6">
                            "Our employees volunteer as mentors through Olilearn's platform. The program
                            has increased employee engagement by 40% while providing quality education to
                            300 students in rural communities."
                        </p>
                        <div class="pt-6 border-t border-emerald-100">
                            <div class="flex justify-between">
                                <span class="text-emerald-800 font-bold">150 Students</span>
                                <span class="text-gray-600">Volunteered</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center text-emerald-600 text-2xl mr-4">
                                🏭
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">Dante Group</h4>
                                <p class="text-emerald-700 font-semibold">Corporate Giving Initiative</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic mb-6">
                            "Our donation through Olilearn has provided STEM education to 1,000
                            students. The platform's analytics help us measure learning outcomes and
                            optimize our educational investments."
                        </p>
                        <div class="pt-6 border-t border-emerald-100">
                            <div class="flex justify-between">
                                <span class="text-emerald-800 font-bold">1,000 Students</span>
                                <span class="text-gray-600">Educational Impact</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-6 py-3 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 text-lg font-medium mb-6">
                        <span class="w-3 h-3 bg-emerald-600 rounded-full mr-2"></span>
                        Simple Partnership Process
                    </div>
                    <h2 class="text-5xl font-bold text-gray-900 mb-6">
                        How Corporate
                        <span class="block text-emerald-800 bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">
                            Partnership Works
                        </span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl font-bold transform transition-transform group-hover:scale-110">
                                1
                            </div>
                            <div class="absolute top-12 right-0 w-8 h-1 bg-emerald-300 hidden md:block"></div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Initial Consultation</h3>
                        <p class="text-gray-700">
                            Schedule a call with our partnership team to discuss your CSR goals and educational impact objectives.
                        </p>
                    </div>

                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl font-bold transform transition-transform group-hover:scale-110">
                                2
                            </div>
                            <div class="absolute top-12 right-0 w-8 h-1 bg-emerald-300 hidden md:block"></div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Program Design</h3>
                        <p class="text-gray-700">
                            We create a customized partnership plan tailored to your budget, timeline, and impact goals.
                        </p>
                    </div>

                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl font-bold transform transition-transform group-hover:scale-110">
                                3
                            </div>
                            <div class="absolute top-12 right-0 w-8 h-1 bg-emerald-300 hidden md:block"></div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Implementation</h3>
                        <p class="text-gray-700">
                            We onboard students, set up your branded portal, and launch your educational program.
                        </p>
                    </div>

                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-center text-white text-3xl font-bold transform transition-transform group-hover:scale-110">
                                4
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Impact Reporting</h3>
                        <p class="text-gray-700">
                            Receive regular reports on student progress, learning outcomes, and social impact metrics.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partnership Form -->
        <section id="partnership-form" class="py-24 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-bold text-gray-900 mb-6">
                        Start Your
                        <span class="block text-emerald-800 bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">
                            Partnership Journey
                        </span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Complete the form below or email us directly at
                        <a href="mailto:hello@olilearn.com" class="text-emerald-700 hover:text-emerald-800 font-semibold">
                            hello@olilearn.com
                        </a>
                    </p>
                </div>

                <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-8 border-2 border-emerald-100 shadow-xl">
                    <form @submit.prevent="submitPartnershipForm" class="space-y-8">
                        <!-- Company Information -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Company Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Name *
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.company_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="Enter company name"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Industry *
                                    </label>
                                    <select
                                        v-model="form.industry"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    >
                                        <option value="" disabled selected>Select industry</option>
                                        <option value="banking">Banking & Finance</option>
                                        <option value="telecom">Telecommunications</option>
                                        <option value="energy">Energy & Utilities</option>
                                        <option value="manufacturing">Manufacturing</option>
                                        <option value="technology">Technology</option>
                                        <option value="retail">Retail & FMCG</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Size *
                                    </label>
                                    <select
                                        v-model="form.company_size"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    >
                                        <option value="" disabled selected>Select company size</option>
                                        <option value="1-50">1-50 employees</option>
                                        <option value="51-200">51-200 employees</option>
                                        <option value="201-500">201-500 employees</option>
                                        <option value="501-1000">501-1,000 employees</option>
                                        <option value="1000+">1,000+ employees</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Annual CSR Budget (₦) *
                                    </label>
                                    <select
                                        v-model="form.csr_budget"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                    >
                                        <option value="" disabled selected>Select budget range</option>
                                        <option value="1-5M">₦1M - ₦5M</option>
                                        <option value="5-10M">₦5M - ₦10M</option>
                                        <option value="10-25M">₦10M - ₦25M</option>
                                        <option value="25-50M">₦25M - ₦50M</option>
                                        <option value="50M+">₦50M+</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Person</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name *
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.contact_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="Enter full name"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Job Title *
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.job_title"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="e.g., CSR Manager"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address *
                                    </label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="contact@company.com"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number *
                                    </label>
                                    <input
                                        type="tel"
                                        v-model="form.phone"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                        placeholder="+234 800 000 0000"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Partnership Interest -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Partnership Interest</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="flex items-center p-4 border border-gray-300 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer transition-all duration-300">
                                        <input
                                            type="checkbox"
                                            v-model="form.interests"
                                            value="csr_sponsorship"
                                            class="h-5 w-5 text-emerald-600 rounded focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 font-medium">CSR Sponsorship</span>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-300 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer transition-all duration-300">
                                        <input
                                            type="checkbox"
                                            v-model="form.interests"
                                            value="corporate_volunteerism"
                                            class="h-5 w-5 text-emerald-600 rounded focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 font-medium">Corporate Volunteerism</span>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-300 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer transition-all duration-300">
                                        <input
                                            type="checkbox"
                                            v-model="form.interests"
                                            value="corporate_giving"
                                            class="h-5 w-5 text-emerald-600 rounded focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 font-medium">Corporate Giving</span>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-300 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer transition-all duration-300">
                                        <input
                                            type="checkbox"
                                            v-model="form.interests"
                                            value="employee_matching"
                                            class="h-5 w-5 text-emerald-600 rounded focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 font-medium">Employee Matching</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tell us about your CSR goals and educational impact objectives *
                            </label>
                            <textarea
                                v-model="form.message"
                                rows="6"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                placeholder="Describe your company's CSR strategy, target beneficiaries, desired impact, and any specific requirements..."
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-5 px-8 rounded-xl transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed text-lg"
                            >
                                <span v-if="submitting" class="flex items-center justify-center">
                                    <svg class="animate-spin h-6 w-6 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting Partnership Request...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    Submit Partnership Request
                                    <svg class="ml-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </span>
                            </button>
                            <p class="text-sm text-gray-600 mt-4 text-center">
                                Or email us directly at
                                <a href="mailto:hello@olilearn.com" class="text-emerald-700 hover:text-emerald-800 font-semibold">
                                    hello@olilearn.com
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Quick Start CTA -->
        <section class="py-24 bg-gradient-to-br from-emerald-900 to-teal-800 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-5xl font-bold mb-6">
                    Ready to Make
                    <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                        Lasting Impact?
                    </span>
                </h2>
                <p class="text-2xl mb-12 text-emerald-100">
                    Join leading Nigerian corporations in transforming education through strategic partnerships.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="text-4xl mb-4">📧</div>
                        <h3 class="text-xl font-bold mb-3">Email Us</h3>
                        <a href="mailto:hello@olilearn.com" class="text-emerald-200 hover:text-white transition-colors">
                            hello@olilearn.com
                        </a>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl mb-4">📞</div>
                        <h3 class="text-xl font-bold mb-3">Call Us</h3>
                        <a href="tel:+2348000000000" class="text-emerald-200 hover:text-white transition-colors">
                            +234 800 000 0000
                        </a>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl mb-4">🚀</div>
                        <h3 class="text-xl font-bold mb-3">Sign Up Directly</h3>
                        <Link
                            :href="route('register')"
                            class="text-emerald-200 hover:text-white transition-colors font-semibold"
                        >
                            Create Corporate Account
                        </Link>
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

const submitting = ref(false);

const form = reactive({
    company_name: '',
    industry: '',
    company_size: '',
    csr_budget: '',
    contact_name: '',
    job_title: '',
    email: '',
    phone: '',
    interests: [],
    message: ''
});

const submitPartnershipForm = async () => {
    submitting.value = true;
    
    try {
        const response = await axios.post('/partnership/submit', form);
        
        if (response.data.success) {
            // Show success message
            alert(response.data.message);
            
            // Reset form
            Object.assign(form, {
                company_name: '',
                industry: '',
                company_size: '',
                csr_budget: '',
                contact_name: '',
                job_title: '',
                email: '',
                phone: '',
                interests: [],
                message: ''
            });
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
            alert('There was an error processing your request. Please try again later or email us directly at hello@olilearn.com');
        }
    } finally {
        submitting.value = false;
    }
};

const scrollToForm = () => {
    const formElement = document.getElementById('partnership-form');
    if (formElement) {
        formElement.scrollIntoView({ behavior: 'smooth' });
    }
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

@keyframes ping-slow {
    0% { transform: scale(1); opacity: 1; }
    75%, 100% { transform: scale(2); opacity: 0; }
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

.animate-ping-slow {
    animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom form styles */
input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Checkbox styling */
input[type="checkbox"] {
    border-radius: 0.375rem;
}

/* Card hover effects */
.transform {
    transition: transform 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:scale-\[1\.02\]:hover {
    transform: scale(1.02);
}

/* Gradient text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .text-5xl {
        font-size: 2.5rem;
    }

    .text-7xl {
        font-size: 3rem;
    }
}

/* Print styles */
@media print {
    button, .hidden-print {
        display: none !important;
    }
}
</style>
