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

// Import Vue Toastification
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import ProfileViewer from "./components/ProfileViewer.vue";

const app = createApp({});

// Use the Toast plugin
app.use(Toast, {
    position: POSITION.TOP_CENTER, // Toast will appear at the top center
    timeout: 5000, // The toast will disappear after 5 seconds
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

// Mount the app
app.mount('#app');
