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

const getUserType = () => {
    const userString = localStorage.getItem('user');
    if (!userString) return null;
    try {
        return JSON.parse(userString).user_type;
    } catch (e) {
        return null;
    }
};

const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: '',
                name: 'Home',
                component: () => {
                    const type = getUserType();
                    if (type === 'B') {
                        return HomeBarber;
                    }
                    return Home;
                },
                meta: { requiresAuth: true }
            }, {
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
            },{
                path: '/agendamento/:barberId',
                name: 'Agendamento',
                component: SchedulingUser,
                meta: { requiresAuth: true }
            },{
                path: '/meusAgendamentos',
                name: 'MeusAgendamentos',
                component: MySchedulesUser,
                meta: { requiresAuth: true }
            },{
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
        component: () => {
            const type = getUserType();
            if (type === 'B') {
                return ProfileBarber;
            }
            return Profile;
        },
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
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

    // If the route requires being a barber and the logged in user is NOT of type 'B', redirected to the Home
    if (requiresBarber && userType !== 'B') {
        return next({ name: 'Home' });
    }

    next();
});

export default router;
