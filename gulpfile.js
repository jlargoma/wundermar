var elixir = require('laravel-elixir');

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

// elixir(function(mix) {
//     mix.sass('app.scss');
// });
/*
elixir(function (mix) {
    mix.styles([
      "css/bootstrap.css",
      "style.css",
      "css/animate.css",
      '/frontend/animate.css',
      '/frontend/magnific-popup.css',
      '/frontend/colors.css',
      '/frontend/rs-plugin/settings.css',
      '/frontend/rs-plugin/layers.css',
      '/frontend/rs-plugin/navigation.css',
      '/frontend/font-icons.css',
      '/frontend/aos.css',
      '/frontend/daterangepicker.css',
      '/frontend/radio-checkbox.css',
      '/frontend/lightslider.css',
      '/frontend/styles.css',
    ], 'public/css/frontend.css');
});

  
elixir(function(mix) {
    mix.combine([
      'resources/assets/js/footer/progressbar.min.js',
      'resources/assets/js/footer/flip.min.js',
      'resources/assets/js/footer/moment.min.js',
      'resources/assets/js/footer/daterangepicker.min.js',
      'resources/assets/js/footer/jquery.flip.min.js',
      'resources/assets/js/loadJS.js',
    ],  'public/js/scripts-ext.js');
});*/
elixir(function(mix) {
    mix.combine([
      'resources/assets/js/footer/plugins.js',
      'resources/assets/js/footer/functions.js',
      'resources/assets/js/footer/scripts.js',
      'resources/assets/js/footer/aos.js',
      'resources/assets/js/form_booking.js'
    ],  'public/js/scripts-footer.js');
});




