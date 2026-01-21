<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
       <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                <img
                src="/logo-olilearn.PNG"
                alt="OliLearn"
                class="h-16 w-auto object-contain"
                />
            </div>
        </div>
    </Link>
</template>
