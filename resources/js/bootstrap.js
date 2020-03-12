window.$ = window.jQuery = require('jquery');
require('popper.js');
require('bootstrap');
const Echo = require('laravel-echo').default;
window.Pusher = require('pusher-js');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.Echo = new Echo({
    broadcaster: 'pusher',
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    key: process.env.MIX_PUSHER_APP_KEY
});
