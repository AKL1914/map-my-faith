// src/stores/auth.js
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
    }),

    actions: {
        setUser(user, token) {
            this.user = user;
            this.token = token;
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
        logout() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            delete window.axios.defaults.headers.common['Authorization'];
        },
        loadUserFromStorage() {
            const user = localStorage.getItem('user');
            const token = localStorage.getItem('token');
            if (user && token) {
                this.user = JSON.parse(user);
                this.token = token;
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
        },
    }
})