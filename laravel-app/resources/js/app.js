import './bootstrap';
import { createApp } from 'vue';

// Import the components
import AdminDashboard from './components/AdminDashboard.vue';
import CampaignManager from './components/CampaignManager.vue';
import MapManager from './components/MapManager.vue';
import UserManager from './components/UserManager.vue';
import PinManager from "./components/PinManager.vue";
import PinViewer from "./components/PinViewer.vue";

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

// Mount the app
app.mount('#app');
