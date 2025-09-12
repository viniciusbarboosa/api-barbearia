import { createRouter, createWebHistory } from 'vue-router';

import MainLayout from '../components/layout/MainLayout.vue';
import Login from '../components/Login.vue';
import Home from '../components/Home.vue';

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      }
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
