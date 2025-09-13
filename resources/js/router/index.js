import { createRouter, createWebHistory } from 'vue-router';

import MainLayout from '../components/layout/MainLayout.vue';
import Login from '../components/Login.vue';
import Home from '../components/Home.vue';
import Profile from '../components/Profile.vue';

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home,
        meta: { requiresAuth: true }
      }
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const token = localStorage.getItem('auth_token');

  if (token && to.name === 'Login') {
    next({ name: 'Home' });
    return;
  }

  if (requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;
