import './bootstrap';
import { createApp } from 'vue';

import AdminDashboard from './components/AdminDashboard.vue';
import CampaignManager from './components/CampaignManager.vue';
import MapManager from './components/MapManager.vue';
import UserManager from './components/UserManager.vue';
import PinManager from "./components/PinManager.vue";
import PinViewer from "./components/PinViewer.vue";

const app = createApp({});
app.component('admin-dashboard', AdminDashboard);
app.component('campaign-manager', CampaignManager);
app.component('map-manager', MapManager);
app.component('user-manager', UserManager);
app.component('pin-manager', PinManager);
app.component('pin-viewer', PinViewer);

app.mount('#app');
