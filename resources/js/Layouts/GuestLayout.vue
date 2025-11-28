<template>
    <div class="font-poppins bg-gray-50 min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <Link :href="route('welcome')" class="flex items-center space-x-2">
                            <img
                            src="/logo-olilearn.PNG"
                            alt="OliLearn"
                            class="h-9 w-auto object-contain"
                            />
                        </Link>
                    </div>

                    <nav class="hidden md:flex space-x-8">
                        <Link :href="route('welcome')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors" :class="{ 'text-emerald-600': $page.component === 'frontpages/Welcome' }">Home</Link>
                        <Link :href="route('features')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors" :class="{ 'text-emerald-600': $page.component === 'frontpages/Features' }">Features</Link>
                        <Link :href="route('courses.index')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">Courses</Link>
                        <Link :href="route('blog.index')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors" :class="{ 'text-emerald-600': $page.component.startsWith('Blog') }">Blog</Link>
                        <Link :href="route('community')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors" :class="{ 'text-emerald-600': $page.component === 'frontpages/Community' }">Community</Link>
                        <Link :href="route('about')" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors" :class="{ 'text-emerald-600': $page.component === 'frontpages/About' }">About</Link>
                    </nav>

                    <div class="flex items-center space-x-4">
                        <Link :href="route('login')" class="px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition">Sign In</Link>
                        <Link :href="route('register')" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-4 py-2 rounded-lg font-medium hover:from-emerald-500 hover:to-teal-500 transition">Get Started</Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <Link :href="route('welcome')" class="flex items-center space-x-2">
                                <img
                                src="/logo-olilearn.PNG"
                                alt="OliLearn"
                                class="h-9 w-auto object-contain"
                                />
                            </Link>
                        </div>
                        <p class="text-gray-400 mb-4">
                            Your intelligent learning companion. Learn smarter, not harder.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><Link :href="route('welcome')" class="text-gray-400 hover:text-emerald-400 transition">Home</Link></li>
                            <li><Link :href="route('courses.index')" class="text-gray-400 hover:text-emerald-400 transition">Courses</Link></li>
                            <li><Link :href="route('features')" class="text-gray-400 hover:text-emerald-400 transition">Features</Link></li>
                            <li><Link :href="route('pricing')" class="text-gray-400 hover:text-emerald-400 transition">Pricing</Link></li>
                            <li><Link :href="route('blog.index')" class="text-gray-400 hover:text-emerald-400 transition">Blog</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2">
                            <li><Link :href="route('help')" class="text-gray-400 hover:text-emerald-400 transition">Help Center</Link></li>
                            <li><Link :href="route('community')" class="text-gray-400 hover:text-emerald-400 transition">Community</Link></li>
                            <li><Link :href="route('contact')" class="text-gray-400 hover:text-emerald-400 transition">Contact Us</Link></li>
                            <li><Link :href="route('faq')" class="text-gray-400 hover:text-emerald-400 transition">FAQ</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Subscribe</h3>
                        <p class="text-gray-400 mb-4">Get the latest news and updates</p>
                        <form @submit.prevent="subscribeNewsletter" class="flex">
                            <input
                                type="email"
                                placeholder="Your email"
                                v-model="newsletterEmail"
                                class="px-4 py-2 rounded-l-lg w-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                required
                            >
                            <button
                                type="submit"
                                class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-r-lg transition"
                                :disabled="newsletterLoading"
                            >
                                <span v-if="newsletterLoading">...</span>
                                <span v-else>→</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500">
                    <p>© 2025 AITutor. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    <PWAInstall />
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const newsletterEmail = ref('');
const newsletterLoading = ref(false);

const subscribeNewsletter = async () => {
    newsletterLoading.value = true;

    // Simulate API call
    setTimeout(() => {
        alert('Thank you for subscribing to our newsletter!');
        newsletterEmail.value = '';
        newsletterLoading.value = false;
    }, 1000);
};
</script>

<style scoped>
/* Gradient Definitions */
.bg-gradient-radial-emerald {
    background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
}

.bg-gradient-radial-teal {
    background: radial-gradient(circle, rgba(20, 183, 167, 0.3) 0%, transparent 70%);
}

.bg-gradient-radial-cyan {
    background: radial-gradient(circle, rgba(14, 165, 233, 0.3) 0%, transparent 70%);
}

/* Floating Elements */
.floating-circle {
    position: absolute;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    filter: blur(20px);
    opacity: 0.6;
    animation: float 8s ease-in-out infinite;
}

.floating-triangle {
    position: absolute;
    width: 100px;
    height: 100px;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    top: 20%;
    right: 10%;
    animation: float 12s ease-in-out infinite;
}

.floating-diamond {
    position: absolute;
    width: 70px;
    height: 70px;
    clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    bottom: 15%;
    left: 15%;
    animation: float 10s ease-in-out infinite;
}

/* Animation Keyframes */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(5deg); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.05); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in-right {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes float-in {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes step-pop {
    0% { transform: scale(0.8); opacity: 0; }
    70% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(3deg); }
}

@keyframes count-up {
    from { transform: scale(0.5); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Apply Animations */
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-in-right {
    animation: slide-in-right 0.8s ease-out forwards;
}

.animate-float-in {
    animation: float-in 0.8s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 2.5s infinite;
}

.animate-step-pop {
    animation: step-pop 0.6s ease-out forwards;
}

.animate-float-slow {
    animation: float-slow 5s ease-in-out infinite;
}

.animate-count-up {
    animation: count-up 1s ease-out forwards;
}

/* Card Effects */
.card-glow {
    position: relative;
    overflow: hidden;
}

.card-glow::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0, transparent 70%);
    transform: scale(0);
    opacity: 0;
    transition: transform 0.5s, opacity 0.5s;
    pointer-events: none;
}

.card-glow:hover::before {
    transform: scale(1);
    opacity: 1;
}

.course-card .course-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(16, 185, 129, 0.2), rgba(20, 183, 167, 0.2));
    opacity: 0;
    transition: opacity 0.3s;
}

.course-card:hover .course-glow {
    opacity: 1;
}

.testimonial-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.testimonial-card:hover {
    transform: translateY(-8px) scale(1.02);
}

/* Stat Items */
.stat-item {
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

/* Ensure proper stacking context */
.relative {
    position: relative;
    z-index: 1;
}
</style>
