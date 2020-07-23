const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/datatable.js', 'public/js')
    .js('resources/js/competition_create.js', 'public/js')
    .js('resources/js/teams_list.js', 'public/js')
    .js('resources/js/add_results.js', 'public/js');
