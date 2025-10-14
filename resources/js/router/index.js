// router/index.js
import { createRouter, createWebHistory } from 'vue-router';

import MainLayout from '../components/layout/MainLayout.vue';
import Login from '../components/Login.vue';
import Home from '../components/Home.vue';
import HomeBarber from '../components/HomeBarber.vue';
import Profile from '../components/Profile.vue';
import ProfileBarber from '../components/ProfileBarber.vue';
import ScheduleManager from '../components/ScheduleManager.vue';
import ServiceManager from '../components/ServiceManager.vue';
import SchedulingUser from '../components/SchedulingUser.vue';
import MySchedulesUser from '../components/MySchedulesUser.vue';
import BarberAppointments from '../components/BarberAppointments.vue';

// Helper functions
const isClient = () => typeof window !== 'undefined';

const getUserType = () => {
    if (!isClient()) return null;

    try {
        const userString = localStorage.getItem('user');
        return userString ? JSON.parse(userString).user_type : null;
    } catch (e) {
        return null;
    }
};

const getHomeComponent = () => {
    if (!isClient()) return Home; // Fallback para SSR

    const type = getUserType();
    return type === 'B' ? HomeBarber : Home;
};

const getProfileComponent = () => {
    if (!isClient()) return Profile; // Fallback para SSR

    const type = getUserType();
    return type === 'B' ? ProfileBarber : Profile;
};

const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: '',
                name: 'Home',
                component: getHomeComponent(),
                meta: { requiresAuth: true }
            },
            {
                path: '/horarios',
                name: 'Horarios',
                component: ScheduleManager,
                meta: { requiresAuth: true, requiresBarber: true }
            },
            {
                path: '/servicos',
                name: 'Servicos',
                component: ServiceManager,
                meta: { requiresAuth: true, requiresBarber: true }
            },
            {
                path: '/agendamento/:barberId',
                name: 'Agendamento',
                component: SchedulingUser,
                meta: { requiresAuth: true }
            },
            {
                path: '/meusAgendamentos',
                name: 'MeusAgendamentos',
                component: MySchedulesUser,
                meta: { requiresAuth: true }
            },
            {
                path: '/agendamentosBarbearia',
                name: 'AgendamentosBarbearia',
                component: BarberAppointments,
                meta: { requiresAuth: true, requiresBarber: true }
            }
        ]
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/perfil',
        name: 'Perfil',
        component: getProfileComponent(),
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (!isClient()) {
        return next();
    }

    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const requiresBarber = to.matched.some(record => record.meta.requiresBarber);
    const token = localStorage.getItem('auth_token');
    const userType = getUserType();

    if (token && to.name === 'Login') {
        return next({ name: 'Home' });
    }

    if (requiresAuth && !token) {
        return next('/login');
    }

    if (requiresBarber && userType !== 'B') {
        return next({ name: 'Home' });
    }

    next();
});

export default router;
