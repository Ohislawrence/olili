<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">

    <title>429 - Too Many Requests | Olilearn</title>

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
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen gradient-bg flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md">
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
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center">
                    <!-- Error Icon -->
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>

                    <!-- Error Code -->
                    <h1 class="text-6xl font-bold text-gray-800 mb-2">429</h1>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Too Many Requests</h2>

                    <!-- Error Message -->
                    <p class="text-gray-600 mb-8">
                        You've sent too many requests in a short amount of time. Please wait a moment and try again.
                    </p>

                    <!-- Retry Timer -->
                    <div class="mb-8">
                        <p class="text-sm text-gray-500 mb-2">Try again in:</p>
                        <div class="text-3xl font-bold text-emerald-600" id="countdown">60</div>
                        <p class="text-sm text-gray-500 mt-2">seconds</p>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-4">
                        <button onclick="window.location.reload()" class="w-full py-3 px-6 btn-gradient text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200 disabled:opacity-50" id="retryBtn" disabled>
                            Try Again (<span id="retryTime">60</span>s)
                        </button>
                        <a href="{{ url('/') }}" class="block w-full py-3 px-6 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition duration-200">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-white/80 text-sm">
                    &copy; {{ date('Y') }} Olilearn. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        let timeLeft = 60;
        const countdownElement = document.getElementById('countdown');
        const retryTimeElement = document.getElementById('retryTime');
        const retryBtn = document.getElementById('retryBtn');

        const countdown = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            retryTimeElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                countdownElement.textContent = 'Now';
                retryTimeElement.textContent = 'Now';
                retryBtn.textContent = 'Try Again';
                retryBtn.disabled = false;
            }
        }, 1000);
    </script>
</body>
</html>
