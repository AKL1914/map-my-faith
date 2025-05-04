import './bootstrap';
import { createApp } from 'vue';
import CampaignManager from './components/CampaignManager.vue';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const app = createApp(CampaignManager); // no App.vue
app.mount('#app');
