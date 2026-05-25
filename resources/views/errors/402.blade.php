<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">

    <title>402 - Payment Required | Olilearn</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    fontFamily: {
                        sans: ['Figtree', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .btn-premium {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .btn-premium:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen gradient-bg flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-lg">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" opacity="0.5"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l-9-5" opacity="0.5"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">Olilearn</span>
                </div>
            </div>

            <!-- Error Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Premium Header -->
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.2 6.5 10.266a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-white">Premium Content</h2>
                    </div>
                    <p class="text-amber-100">Unlock exclusive learning materials</p>
                </div>

                <div class="p-8">
                    <div class="text-center">
                        <!-- Error Icon -->
                        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6 pulse">
                            <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <!-- Error Code -->
                        <h1 class="text-6xl font-bold text-gray-800 mb-2">402</h1>
                        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Payment Required</h2>

                        <!-- Error Message -->
                        <p class="text-gray-600 mb-6">
                            This is premium content that requires a subscription or one-time payment to access.
                        </p>

                        <!-- Features List -->
                        <div class="bg-emerald-50 rounded-xl p-6 mb-8 border border-emerald-200">
                            <h3 class="font-semibold text-emerald-800 mb-4">What you'll get with Premium:</h3>
                            <ul class="space-y-3 text-left">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Full access to all course materials</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Certificates</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Priority support & mentorship</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Exclusive practice exams & quizzes</span>
                                </li>
                            </ul>
                        </div>



                        <!-- Alternative Options -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-3">Get access:</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('pricing') }}" class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                                    See Plans
                                </a>
                                <a href="{{ route('pricing') }}" class="flex-1 py-2 px-4 border border-emerald-300 text-emerald-600 font-medium rounded-lg hover:bg-emerald-50 transition text-center">
                                    Apply for Scholarship
                                </a>
                            </div>
                        </div>

                        <!-- Back Options -->
                        <div class="mt-8">
                            <a href="{{ url()->previous() }}" class="text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Go Back
                            </a>
                            <span class="mx-3 text-gray-300">•</span>
                            <a href="{{ route('courses.index') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                                Browse Free Courses
                            </a>
                        </div>

                        <!-- Trust Badges -->
                        <div class="mt-8 flex flex-wrap justify-center items-center gap-6 text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">Secure Payment</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.2 6.5 10.266a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">More Courses</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-sm">Rated 4.8/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-white/80 text-sm">
                    &copy; {{ date('Y') }} Olilearn. All rights reserved.
                </p>
                <p class="text-white/60 text-xs mt-2">
                    Need help choosing a plan? <a href="{{ route('contact') }}" class="underline hover:text-white">Contact our sales team</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function proceedToCheckout(planType) {
            // Show loading state
            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => {
                btn.disabled = true;
                if (btn.textContent.includes('Get')) {
                    btn.innerHTML = `
                        <svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    `;
                }
            });

            // Simulate API call to create checkout session
            setTimeout(() => {
                // In a real app, this would redirect to Stripe/Paddle checkout
                const plan = planType === 'annual' ? 'annual' : 'monthly';

                // Redirect to checkout page
                window.location.href = `/checkout?plan=${plan}&redirect_url=${encodeURIComponent(window.location.href)}`;
            }, 1000);
        }

        // Add any coupon code functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a coupon in URL
            const urlParams = new URLSearchParams(window.location.search);
            const coupon = urlParams.get('coupon');

            if (coupon) {
                // Auto-apply coupon
                document.getElementById('coupon-message').innerHTML = `
                    <div class="mt-4 p-3 bg-emerald-100 border border-emerald-300 rounded-lg">
                        <p class="text-emerald-800 text-sm">
                            Coupon <strong>${coupon}</strong> applied! Discount will be shown at checkout.
                        </p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
