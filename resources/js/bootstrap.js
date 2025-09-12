// ensure crypto polyfill is loaded for older node/vite builds
import '/vite-crypto-polyfill.cjs';
import axios from 'axios';
window.axios = axios;

// default base for API calls
window.axios.defaults.baseURL = '/api';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// if csrf token meta exists (blade), set it
const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
}
