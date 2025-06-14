import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './pages/HomePage.vue';
import LoginPage from './pages/LoginPage.vue';
import ActivatePage from './pages/ActivatePage.vue';
import MapManager from './components/MapManager.vue';
function isAuthenticated() {
    return localStorage.getItem('token') !== null;
}

function isActivated() {
    try {
        const user = JSON.parse(localStorage.getItem('user'));
        return user?.is_activated === 'true' || user?.is_activated === true;
    } catch {
        return false;
    }
}

const routes = [
    { path: '/', component: HomePage },
    { path: '/login-success', component: LoginPage },
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

    router.beforeEach((to, from, next) => {
        if (to.meta.requiresAuth && !isAuthenticated()) {
            return next('/login-success');
        }

        if (to.meta.requiresActivation && !isActivated()) {
            return next('/activate');
        }

        next();
    });

export default router;