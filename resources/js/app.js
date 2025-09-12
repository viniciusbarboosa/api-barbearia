import './bootstrap';
import { createApp } from 'vue';
import Example from './components/Example.vue';

const appDiv = document.getElementById('app');
if (appDiv) {
	const app = createApp(Example);
	app.mount('#app');
}
