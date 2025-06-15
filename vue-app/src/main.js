import { createApp } from 'vue';
import App from './App.vue';
import './style.css';
import './bootstrap'; // axios, leaflet, etc.
import router from './router';
import { createPinia } from 'pinia';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const API_BASE_URL = 'http://localhost:8080';
// window.API_BASE_URL = 'http://localhost:8080';
axios.defaults.baseURL = API_BASE_URL;
// Create Pinia instance
const pinia = createPinia();

const app = createApp(App);

// Use Pinia and other plugins
app.use(pinia);
app.use(router);
app.use(Toast, {
    position: 'top-center',
    timeout: 5000,
});

// Now import the auth store and load user before mount
import { useAuthStore } from '@/stores/auth';
const authStore = useAuthStore();
authStore.loadUserFromStorage();

// Finally mount the app
app.mount('#wrapper');