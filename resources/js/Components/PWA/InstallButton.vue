<template>
    <div v-if="showInstallButton" class="pwa-install-banner">
        <div class="install-content">
            <p>Install Olilearn for a better learning experience</p>
            <button @click="installPWA" class="install-btn">
                Install
            </button>
            <button @click="dismissInstall" class="dismiss-btn">
                Not Now
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showInstallButton = ref(false);
let deferredPrompt = null;

onMounted(() => {
    // Listen for beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallButton.value = true;
    });

    // Check if already installed
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                         window.navigator.standalone === true;
    if (isStandalone) {
        showInstallButton.value = false;
    }
});

const installPWA = async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('User accepted the install prompt');
        }
        deferredPrompt = null;
        showInstallButton.value = false;
    }
};

const dismissInstall = () => {
    showInstallButton.value = false;
    // Store dismissal in localStorage
    localStorage.setItem('pwaDismissed', 'true');
};
</script>

<style scoped>
.pwa-install-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 16px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    border-top: 1px solid #e5e7eb;
}

.install-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

.install-btn {
    background: #3b82f6;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
}

.dismiss-btn {
    background: #6b7280;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    margin-left: 8px;
}
</style>
