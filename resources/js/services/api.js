import axios from 'axios';

const baseURL = '/api';

const instance = axios.create({
  baseURL,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// attach token if present
const token = localStorage.getItem('auth_token');
if (token) {
  instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default instance;
