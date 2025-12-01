<template>
    <Head title="Log in" />
<!-- Flash Messages -->
        <div v-if="$page.props.flash.success" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ $page.props.flash.success }}
        </div>
        
        <div v-if="$page.props.flash.error" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ $page.props.flash.error }}
        </div>
        
        <div v-if="$page.props.flash.info" class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ $page.props.flash.info }}
        </div>
    <!-- Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-emerald-100 rounded-full opacity-30 animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-teal-100 rounded-full opacity-40 animate-float-delay"></div>
        <div class="absolute top-1/3 right-1/3 w-32 h-32 bg-cyan-100 rounded-full opacity-50 animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Login Container -->
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-slate-50 to-white">
        <div class="w-full max-w-md relative z-10">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="mb-4 flex justify-center">
                    <img
                        src="/logo-olilearn.PNG"
                        alt="OliLearn"
                        class="h-16 w-auto object-contain"
                    />
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Sign in to your Olilearn account</p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                {{ status }}
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <!-- Social Login Buttons -->
    <div class="mb-6" v-if="availableProviders.length > 0">
        <div class="grid grid-cols-1 gap-3">
            <!-- Google Login -->
            <a 
                v-if="availableProviders.includes('google')"
                :href="route('social.redirect', { provider: 'google' })" 
                class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors hover:shadow-md"
            >
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>

            <!-- GitHub Login -->
            <a 
                v-if="availableProviders.includes('github')"
                :href="route('social.redirect', { provider: 'github' })" 
                class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors hover:shadow-md"
            >
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                </svg>
                Continue with GitHub
            </a>

            <!-- Facebook Login -->
            <a 
                v-if="availableProviders.includes('facebook')"
                :href="route('social.redirect', { provider: 'facebook' })" 
                class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors hover:shadow-md"
            >
                <svg class="w-5 h-5 mr-3" fill="#1877F2" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Continue with Facebook
            </a>
        </div>
    </div>

    <!-- Divider (only show if there are social buttons) -->
    <div v-if="availableProviders.length > 0" class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with email</span>
        </div>
    </div>


                <form @submit.prevent="submit">
                    <!-- Email Field -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="Enter your email"
                            />
                        </div>
                        <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            />
                        </div>
                        <div v-if="form.errors.password" class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                            />
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                            Forgot password?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                        <span v-else>Sign In</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <Link :href="route('register')" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                            Sign up now
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    &copy; 2026 Olilearn. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm,usePage } from '@inertiajs/vue3';
import { computed,onMounted, watch } from 'vue';

const page = usePage()

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    availableProviders: {
        type: Array,
        default: () => ['google', 'github'] // Default providers
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    if (page.props.flash.success || page.props.flash.error || page.props.flash.info) {
        setTimeout(() => {
            // Clear flash messages
            page.props.flash.success = null
            page.props.flash.error = null
            page.props.flash.info = null
        }, 5000)
    }
})
</script>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delay {
    animation: float 7s ease-in-out infinite;
    animation-delay: 1s;
}

.form-input:focus {
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}
</style>