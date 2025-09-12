import './bootstrap';
import { createApp } from 'vue';
import Login from './components/Login.vue';

const appDiv = document.getElementById('app');
if (appDiv) {
	const app = createApp(Login);
	app.mount('#app');
}
