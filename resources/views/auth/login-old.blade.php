<!DOCTYPE html>
<html class="ie9 no-focus"> 
    <head>
        <meta charset="utf-8">

        <title>Administración Reservas MiramarSKI</title>

        <meta name="description" content="Administración Reservas MiramarSKI">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

        <link rel="shortcut icon" href="{{ asset('/admin-css/assets/img/favicons/favicon.png') }}">

        <link rel="icon" type="image/png" href="{{ asset('/admin-css/assets/img/favicons/favicon-16x16.png') }}" sizes="16x16">
        <link rel="icon" type="image/png" href="{{ asset('/admin-css/assets/img/favicons/favicon-32x32.png') }}" sizes="32x32">
        <link rel="icon" type="image/png" href="{{ asset('/admin-css/assets/img/favicons/favicon-96x96.png') }}" sizes="96x96">
        <link rel="icon" type="image/png" href="{{ asset('/admin-css/assets/img/favicons/favicon-160x160.png') }}" sizes="160x160">
        <link rel="icon" type="image/png" href="{{ asset('/admin-css/assets/img/favicons/favicon-192x192.png') }}" sizes="192x192">

        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/admin-css/assets/img/favicons/apple-touch-icon-180x180.png') }}">

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">

        <link rel="stylesheet" href="{{ asset('/admin-css/assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" id="css-main" href="{{ asset('/admin-css/assets/css/oneui.css') }}">
    </head>
    <body>
        <!-- Login Content -->
        <div class="bg-white pulldown">
            <div class="content content-boxed overflow-hidden">
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                        <div class="push-30-t push-50 animated fadeIn">
                            <div class="row text-center">
                                <div class="col-xs-4 col-xs-offset-4">
                                    <img class="img-responsive" align="middle" src="{{ asset ('/admin-css/assets/logo-negro.png')}}"/>
                                </div>
                                <div style="clear: both;"></div>
                                <p class="text-muted push-15-t">Administrador de reservas de MiramarSKI</p>
                            </div>
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Contraseña</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password">

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember"> Recuerdame
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4 text-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-btn fa-sign-in"></i> Login
                                        </button>
                                        <br>
                                        <a class="btn btn-link" href="{{ url('/password/reset') }}">Has olvidado tu contraseña?</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Login Content -->

        <!-- Login Footer -->
        <div class="pulldown push-30-t text-center animated fadeInUp">
            <small class="text-muted"><span class="js-year-copy"></span> &copy; MiramarSKI</small>
        </div>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.scrollLock.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.appear.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.countTo.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/jquery.placeholder.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/core/js.cookie.min.js') }}"></script>
        <script src="{{ asset('/admin-css/assets/js/app.js') }}"></script>
    </body>
</html>

