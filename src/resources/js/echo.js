import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.PUSHER_APP_KEY,
    wsHost: import.meta.env.PUSHER_HOST,
    wsPort: import.meta.env.PUSHER_URL ?? 'localhost',
    wssPort: import.meta.env.PUSHER_PORT ?? 6001,
    forceTLS: (import.meta.env.PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel(`user.1`).listen("RoleApproved", (data) => {
    console.log("Received event:", data);
});
