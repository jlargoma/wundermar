<!DOCTYPE html>
<html class="no-focus" lang="en"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">

    <title>Error 404 - {{config('app.site')}}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
      h1 {
    font-size: 7em;
    color: #6d6d6d;
    font-family: serif;
    font-style: oblique;
}
h2 {
    color: #4c4c4c;
    margin-bottom: 3em;
}
a.site-name{
    font-size: 2em;
    font-style: italic;
    text-decoration: none;
}
    </style>
  </head>
  <body>
    <!-- Error Content -->
    <div class="container">
      <div style="width: 80%; margin: 3em auto; text-align: center;">
        <a href="/" title="Ir al inicio" class="site-name">{{config('app.site')}}</a>
        <!-- Error Titles -->
        <h1>404</h1>
        <h2>Ups!! La url ingresada no existe en nuestro sistema.</h2>
        <!-- END Error Titles -->
        <a href="/" title="Ir al inicio">Volver al Inicio</a><br/>
      </div>
    </div>
    <!-- END Error Content -->

    <!-- OneUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock, Appear, CountTo, Placeholder, Cookie and App.js -->
  </body>
</html>