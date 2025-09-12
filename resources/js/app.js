import './bootstrap';
import { createApp } from 'vue';
import Login from './components/Login.vue';
import Home from './components/Home.vue';

const appDiv = document.getElementById('app');
if (appDiv) {
	const app = createApp(Home);
	app.mount('#app');
}
