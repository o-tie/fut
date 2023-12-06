const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/scss/app.scss', 'public/css')
    .vue()
    .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/fonts');
