import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Views
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Documentation from '../views/Documentation.vue';
import Dashboard from '../views/Dashboard.vue';
import Parties from '../views/Parties.vue';
import Products from '../views/Products.vue';
import Bills from '../views/Bills.vue';
import CreateBill from '../views/CreateBill.vue';
import ViewBill from '../views/ViewBill.vue';
import EwayBills from '../views/EwayBills.vue';
import Settings from '../views/Settings.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true },
    },
    {
        path: '/documentation',
        name: 'documentation',
        component: Documentation,
        meta: { public: true },
    },
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/parties',
        name: 'parties',
        component: Parties,
        meta: { requiresAuth: true },
    },
    {
        path: '/products',
        name: 'products',
        component: Products,
        meta: { requiresAuth: true },
    },
    {
        path: '/bills',
        name: 'bills',
        component: Bills,
        meta: { requiresAuth: true },
    },
    {
        path: '/bills/create',
        name: 'create-bill',
        component: CreateBill,
        meta: { requiresAuth: true },
    },
    {
        path: '/bills/:id',
        name: 'view-bill',
        component: ViewBill,
        meta: { requiresAuth: true },
    },
    {
        path: '/eway-bills',
        name: 'eway-bills',
        component: EwayBills,
        meta: { requiresAuth: true },
    },
    {
        path: '/settings',
        name: 'settings',
        component: Settings,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    // Public routes (like documentation) are always accessible
    if (to.meta.public) {
        next();
    } else if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;
