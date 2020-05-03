/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Menu active item
let page = window.location.pathname.split('/')[1];
$('a[href*="' + page + '"].nav-link').addClass('active');
if (page === '') {
    $('a[href="/"]').addClass('active');
}
