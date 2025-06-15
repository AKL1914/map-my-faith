import { createRouter, createWebHistory } from 'vue-router';
import PublicLayout from './layouts/PublicLayout.vue';
import AuthLayout from './layouts/AuthLayout.vue';
import HomePage from './pages/HomePage.vue';
import LoginPage from './pages/LoginPage.vue';
import ActivatePage from './pages/ActivatePage.vue';
import MapManager from './components/MapManager.vue';
import UserPinsViewer from "@/components/UserPinsViewer.vue";
import ProfileViewer from "@/components/ProfileViewer.vue";
import Leaderboard from "@/components/Leaderboard.vue";
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
    {
        path: '/',
        component: PublicLayout,
        children: [
            { path: '', component: HomePage },
            { path: 'login-success', component: LoginPage },
            { path: 'activate', component: ActivatePage },
        ]
    },
    {
        path: '/maps',
        component: AuthLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '', component: MapManager }
        ]
    },
    {
        path: '/leaderboard',
        component: AuthLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '', component: Leaderboard }
        ]
    },
    {
        path: '/user/pins',
        component: AuthLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '', component: UserPinsViewer }
        ]
    },
    {
        path: '/profile/edit',
        component: AuthLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '', component: ProfileViewer }
        ]
    }
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