<!-- resources/js/Components/MetaTags.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: 'Your AI learning companion: Create custom paths, get instant answers, and master any subject with personalized guidance.'
    },
    image: {
        type: String,
        default: '/images/og-image.jpg'
    },
    url: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'website'
    },
    twitterSite: {
        type: String,
        default: '@olilearn'
    }
});

// Generate full URL for OG tags
const fullUrl = props.url || window.location.href;
const fullImageUrl = props.image.startsWith('http') ? props.image : `${window.location.origin}${props.image}`;
</script>

<template>
    <Head>
        <!-- Primary Meta Tags -->
        <title>{{ title }} - AITutor</title>
        <meta name="title" :content="title">
        <meta name="description" :content="description">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="type">
        <meta property="og:url" :content="fullUrl">
        <meta property="og:title" :content="title">
        <meta property="og:description" :content="description">
        <meta property="og:image" :content="fullImageUrl">
        <meta property="og:site_name" content="AITutor">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" :content="fullUrl">
        <meta property="twitter:title" :content="title">
        <meta property="twitter:description" :content="description">
        <meta property="twitter:image" :content="fullImageUrl">
        <meta property="twitter:site" :content="twitterSite">
    </Head>
</template>
