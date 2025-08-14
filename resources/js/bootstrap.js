/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios'
window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

import Echo from 'laravel-echo'

import Pusher from 'pusher-js'
window.Pusher = Pusher

// Only initialize Echo if PUSHER_APP_KEY is defined in the environment
const appKey = import.meta.env.VITE_PUSHER_APP_KEY || 'staging';
const appHost = import.meta.env.VITE_PUSHER_HOST || window.location.hostname;
const appPort = import.meta.env.VITE_PUSHER_PORT || 6001;
const appTLS = import.meta.env.VITE_PUSHER_SCHEME === 'https';

// Check if we're in a development environment
const isDev = import.meta.env.DEV || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

// Only initialize Echo if we're not in development mode or if explicitly enabled
if (!isDev || import.meta.env.VITE_ENABLE_PUSHER_IN_DEV) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: appKey,
        wsHost: appHost,
        wsPort: appPort,
        forceTLS: appTLS,
        disableStats: true,
        // Add a cluster if specified
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
        // Enable encrypted if using TLS
        encrypted: appTLS
    });
} else {
    console.log('Echo/Pusher disabled in development environment');
}

