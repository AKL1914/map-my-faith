import axios from 'axios';
import debounce from 'lodash/debounce';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.debounce = debounce;
window.axios = axios;
window.L = L;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token from <meta> tag
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: Please make sure <meta name="csrf-token"> is in your HTML head.');
}
