// I HATE ELIXIR NOTIFICATION!!!
process.env.DISABLE_NOTIFIER = true;

var elixir  = require('laravel-elixir');
var gulp    = require('gulp');
var htmlmin = require('gulp-htmlmin');

/*
 |--------------------------------------------------------------------------
 | Additional tasks
 |--------------------------------------------------------------------------
 |
 | You can provide any tasks here
 |
 */

// Compressing compiled view, so it's minified!
// Use it with `mix.compress()`
elixir.extend('compress', function() {
  new elixir.Task('compress', function() {
    return gulp
      .src('./storage/framework/views/*')
      .pipe(htmlmin({
        removeComments: true,
        keepClosingSlash: false,
        collapseWhitespace: true,
        removeAttributeQuotes: true,
        collapseBooleanAttributes: true,
        collapseInlineTagWhitespace: true,
      }))
      .pipe(gulp.dest('./storage/framework/views/'));
  });
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
  mix.compress();

  mix.sass('app.scss', 'public/css/app.bundle.css');

  mix.copy('bower_components/roboto/fonts', 'public/fonts/roboto');
  mix.copy('resources/assets/fonts/material-design-icon', 'public/fonts/material-design-icon');

  mix.scripts([
    'head.js',
  ], 'public/js/head.bundle.js');

  mix.scripts([
    './bower_components/material-design-lite/material.js',
    'app.js',
  ], 'public/js/app.bundle.js');
});
