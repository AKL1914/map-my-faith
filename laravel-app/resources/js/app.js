// Register service worker early for Vue dev server on localhost:8080
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .catch(() => {
            // You can optionally log errors or report to an error tracker
        });
}

import './bootstrap';
import { createApp } from 'vue';

// Import the components
import AdminDashboard from './components/AdminDashboard.vue';
import CampaignManager from './components/CampaignManager.vue';
import MapManager from './components/MapManager.vue';
import UserManager from './components/UserManager.vue';
import PinManager from "./components/PinManager.vue";
import PinViewer from "./components/PinViewer.vue";
import EventManager from "./components/admin/EventManager.vue";
import EventParticipants from "./components/admin/EventParticipants.vue";
import EventPins from "./components/admin/EventPins.vue";
import GamePinManager from "./components/admin/GamePinManager.vue";
import ProfileViewer from "./components/ProfileViewer.vue";
import UserPinsViewer from "./components/UserPinsViewer.vue";
import ChartPie from "./components/admin/ChartPie.vue";
import ChartArea from "./components/admin/PinsByAreaChart.vue";
import SettingManager from "./components/admin/SettingManager.vue";

// Vue Toastification
import Toast, { POSITION, useToast } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp({});

// Use Toast plugin
app.use(Toast, {
    position: POSITION.TOP_CENTER,
    timeout: 5000,
});

// Register components
app.component('admin-dashboard', AdminDashboard);
app.component('campaign-manager', CampaignManager);
app.component('map-manager', MapManager);
app.component('user-manager', UserManager);
app.component('pin-manager', PinManager);
app.component('pin-viewer', PinViewer);
app.component('profile-viewer', ProfileViewer);
app.component('admin-event-manager', EventManager);
app.component('admin-event-participants', EventParticipants);
app.component('admin-event-pins', EventPins);
app.component('admin-game-pin-manager', GamePinManager);
app.component('user-pins-viewer', UserPinsViewer);
app.component('admin-chart-pie', ChartPie);
app.component('admin-chart-area', ChartArea);
app.component('admin-setting-manager', SettingManager);

// 🔔 Push subscription logic
const subscribeUserIfNeeded = async () => {
    try {
        const registration = await navigator.serviceWorker.ready;
        const existingSubscription = await registration.pushManager.getSubscription();

        if (!existingSubscription) {
            const vapidKey = document.querySelector('meta[name="vapid-key"]')?.content;
            if (!vapidKey) return;

            const newSubscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: vapidKey,
            });

            await axios.post('/api/save-subscription', newSubscription);
        }
    } catch (err) {
        // Optionally handle or report error
    }
};

if ('serviceWorker' in navigator && 'PushManager' in window) {
    window.addEventListener('load', () => {
        if (Notification.permission === 'granted') {
            subscribeUserIfNeeded();
        } else {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    subscribeUserIfNeeded();
                }
            });
        }
    });
}

// Mount the app
app.mount('#app');

// Toast instance
const toast = useToast();

// Push message listener
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', event => {
        if (event.data && event.data.type === 'push-notification') {
            const level = event.data.data.level || 'info';
            if (toast[level]) {
                toast[level](event.data.data.body || 'You have a new notification', {
                    timeout: 7000,
                    title: event.data.title || 'Notification',
                });
            } else {
                toast.info(event.data.data.body || 'You have a new notification', {
                    timeout: 7000,
                    title: event.data.data.title || 'Notification',
                });
            }
        }
    });
}
