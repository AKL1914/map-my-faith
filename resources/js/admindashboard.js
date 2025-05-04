import './bootstrap';
import { createApp } from 'vue';
import AdminDashboard from './components/AdminDashboard.vue';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const app = createApp(AdminDashboard); // no App.vue
app.mount('#app');
