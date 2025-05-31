import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './pages/HomePage.vue';
import LoginPage from './pages/LoginPage.vue';
import ActivatePage from './pages/ActivatePage.vue';
import MapManager from './components/MapManager.vue';

// Simulated auth check
function isAuthenticated() {
    return localStorage.getItem('user') !== null;
}

const routes = [
    { path: '/', component: HomePage },
    { path: '/login', component: LoginPage },
    { path: '/activate', component: ActivatePage },
    {
        path: '/maps',
        component: MapManager,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Global route guard
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next('/login');
    } else {
        next();
    }
});

export default router;
