import './bootstrap';
import { createApp } from 'vue';
import UserManager from './components/UserManager.vue';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const app = createApp(UserManager); // no App.vue
app.mount('#app');
