import { createApp } from 'vue';
import App from './App.vue';
import './style.css';
import './bootstrap'; // axios, leaflet, etc.
import router from './router';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);

app.use(router);
app.use(Toast, {
    position: 'top-center',
    timeout: 5000,
});

app.mount('#app');
