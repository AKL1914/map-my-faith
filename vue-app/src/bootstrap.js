import axios from 'axios';
import debounce from 'lodash/debounce';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.debounce = debounce;
window.axios = axios;
window.L = L;

// Set Authorization header if token exists in localStorage
const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Optional: Axios interceptor to handle 401 unauthorized errors globally
axios.interceptors.response.use(
    response => response,
    error => {
        const { response } = error;

        // if (response && response.status === 401) {
        //     // Token expired or invalid - redirect to login or activation page
        //     window.location.href = '/activate';
        // }

        return Promise.reject(error);
    }
);