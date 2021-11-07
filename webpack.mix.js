const mix = require('laravel-mix');

mix
.js('resources/js/app.js', 'public/js/app.min.js')
.postCss('resources/css/app.css', 'public/css/app.min.css', [])
.options({ processCssUrls: true });

mix
.copyDirectory('resources/images', 'public/images');
