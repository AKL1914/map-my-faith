import './bootstrap';
import { createApp } from 'vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Map from './components/Map.vue';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const app = createApp(Map); // no App.vue
app.component('admin-dashboard', AdminDashboard);
app.component('map', Map);
app.mount('#app');
