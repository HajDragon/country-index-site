/**
 * Bootstrap file for initializing Laravel application
 * Includes Axios and other shared dependencies
 */

// Configure Axios for CSRF token
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
