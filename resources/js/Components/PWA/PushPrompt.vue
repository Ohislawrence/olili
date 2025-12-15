<template>
  <div v-if="showPrompt" class="push-prompt-overlay">
    <div class="push-prompt-modal">
      <div class="push-prompt-header">
        <div class="push-prompt-icon">🔔</div>
        <h3>Stay Updated!</h3>
      </div>

      <div class="push-prompt-body">
        <p>Get instant notifications about:</p>
        <ul>
          <li>New messages</li>
          <li>Order updates</li>
          <li>Important announcements</li>
          <li>Special offers</li>
        </ul>

        <div class="push-prompt-actions">
          <button @click="enableNotifications" class="btn-primary" :disabled="loading">
            {{ loading ? 'Enabling...' : 'Yes, Enable Notifications' }}
          </button>
          <button @click="dismissPrompt" class="btn-secondary">
            Not Now
          </button>
          <button @click="blockPrompt" class="btn-link">
            Never Ask Again
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const showPrompt = ref(false);
const loading = ref(false);
const isSupported = ref(false);

const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;

onMounted(() => {
  checkSupport();
  checkPermission();
});

const checkSupport = () => {
  isSupported.value = 'serviceWorker' in navigator &&
                     'PushManager' in window &&
                     'Notification' in window;
};

const checkPermission = async () => {
  if (!isSupported.value) return;

  // Check if already granted or denied
  const permission = Notification.permission;

  if (permission === 'granted') {
    // Already subscribed, no need to show prompt
    return;
  }

  if (permission === 'denied') {
    // User blocked notifications
    localStorage.setItem('pushBlocked', 'true');
    return;
  }

  // Check if user has dismissed before
  const dismissed = localStorage.getItem('pushDismissed');
  const blocked = localStorage.getItem('pushBlocked');

  if (!dismissed && !blocked) {
    // Wait a bit before showing prompt
    setTimeout(() => {
      showPrompt.value = true;
    }, 3000); // Show after 3 seconds
  }
};

const enableNotifications = async () => {
  loading.value = true;

  try {
    // Request permission
    const permission = await Notification.requestPermission();

    if (permission !== 'granted') {
      alert('Please enable notifications in your browser settings.');
      showPrompt.value = false;
      loading.value = false;
      return;
    }

    // Register service worker if not already
    if (!navigator.serviceWorker.controller) {
      await navigator.serviceWorker.register('/sw.js');
    }

    const registration = await navigator.serviceWorker.ready;

    // Subscribe to push
    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
    });

    // Prepare subscription data
    const key = subscription.getKey('p256dh');
    const auth = subscription.getKey('auth');

    const subscriptionData = {
      endpoint: subscription.endpoint,
      keys: {
        p256dh: key ? btoa(String.fromCharCode(...new Uint8Array(key))) : '',
        auth: auth ? btoa(String.fromCharCode(...new Uint8Array(auth))) : ''
      }
    };

    // Send to server
    await router.post('/api/push/subscribe', subscriptionData);

    // Hide prompt
    showPrompt.value = false;

    // Send welcome notification
    setTimeout(() => {
      router.post('/api/push/test', {
        title: 'Notifications Enabled! 🎉',
        body: 'You\'ll now receive important updates from our app.',
        icon: '/icons/icon-192x192.png',
        url: window.location.href
      });
    }, 1000);

  } catch (error) {
    console.error('Error enabling notifications:', error);
    alert('Failed to enable notifications. Please try again.');
  } finally {
    loading.value = false;
  }
};

const dismissPrompt = () => {
  showPrompt.value = false;
  localStorage.setItem('pushDismissed', new Date().toISOString());
};

const blockPrompt = () => {
  showPrompt.value = false;
  localStorage.setItem('pushBlocked', 'true');
};

// Helper function
function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/-/g, '+')
    .replace(/_/g, '/');
  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }

  return outputArray;
}
</script>

<style scoped>
.push-prompt-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.push-prompt-modal {
  background: white;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  animation: slideIn 0.3s ease-out;
}

.push-prompt-header {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 24px;
  border-radius: 12px 12px 0 0;
  text-align: center;
}

.push-prompt-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.push-prompt-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
}

.push-prompt-body {
  padding: 24px;
}

.push-prompt-body p {
  margin: 0 0 16px 0;
  color: #4b5563;
  font-size: 14px;
}

.push-prompt-body ul {
  margin: 0 0 24px 0;
  padding-left: 20px;
  color: #6b7280;
  font-size: 13px;
}

.push-prompt-body li {
  margin-bottom: 6px;
}

.push-prompt-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

.btn-secondary {
  background: #f0fdf4;
  color: #065f46;
  border: 1px solid #a7f3d0;
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #dcfce7;
  border-color: #86efac;
  transform: translateY(-1px);
}

.btn-link {
  background: transparent;
  color: #6b7280;
  border: none;
  padding: 8px;
  font-size: 13px;
  cursor: pointer;
  text-decoration: underline;
  transition: color 0.2s;
}

.btn-link:hover {
  color: #374151;
}

@keyframes slideIn {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>