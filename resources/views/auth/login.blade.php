<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Pages - Admin Dashboard UI Kit - Lock Screen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
    <link rel="apple-touch-icon" href="pages/ico/60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="pages/ico/76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="pages/ico/120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="pages/ico/152.png">
    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <!--[if lte IE 9]>
        <link href="pages/css/ie9.css" rel="stylesheet" type="text/css" />
    <![endif]-->
    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
  <?php 
  use App\Classes\Mobile;
  $mobile = new Mobile();
  $imglockscreen = asset('img/riad/lockscreen.jpg');
  ?>

  </head>
  <body class="fixed-header ">



    <div class="login-wrapper ">
      <!-- START Login Background Pic Wrapper-->
      <div class="bg-pic" >
        <!-- START Background Pic-->

        <img src="{{ $imglockscreen }}" data-src="{{ $imglockscreen }}" data-src-retina="{{ $imglockscreen }}" alt="" class="lazy" style="opacity: 1 !important;min-width: 100%!important;float: right;">
        <!-- END Background Pic-->
        <!-- START Background Caption-->
        <div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">

        </div>
        <!-- END Background Caption-->
      </div>
      <!-- END Login Background Pic Wrapper-->

      <!-- START Login Right Container-->
      <div class="login-container " style="float: left;background-color: rgba(2,2,2,0.5);">
        <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
          <h2 class="semi-bold text-white" > Gestion Riad Puertas del Albaicín </h2>
          <p class="p-t-35 text-white">Logueate para acceder a tu cuenta</p>
          <!-- START Login Form -->
          <form id="form-login" class="p-t-15" role="form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>Email</label>
              <div class="controls">
                <input type="text" name="email" placeholder="Email" class="form-control" required>
              </div>
            </div>
            <!-- END Form Control-->
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>Password</label>
              <div class="controls">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
            </div>
            <!-- START Form Control-->
            <div class="row" style="color: #FFF">
              <div class="col-md-6 no-padding">
                <div class="checkbox ">
                  <input type="checkbox" value="1" id="checkbox1">
                  <label for="checkbox1">Recuerdame</label>
                </div>
              </div>
              <div class="col-md-6 text-right">
                <a href="{{ url('/password/reset') }}" class="reset small" >Olvide mi contraseña</a>
              </div>
            </div>
            <!-- END Form Control-->
            <button class="btn btn-primary btn-cons m-t-10" type="submit">Login </button>
            
            <a href="/clear-cookies" class="clearCookie">Limpiar Cookies</a>
          </form>

          <div style="margin-top: 1em;">
          @if($errors->any())
          <p class="alert alert-danger">{{$errors->first()}}</p>
          @endif
          </div>
          <!--END Login Form-->
        </div>
      </div>
      <!-- END Login Right Container-->

    </div>

    <style>
      .reset{
            color: #FFF !important;
    margin-top: 12px;
    display: block;
    font-size: 14px;

      }
      .clearCookie{
        float: right;
        margin-top: 10px;
        background-color: #FFF;
        padding: 7px 11px;
        border-radius: 3px;
      }
    </style>
  </body>
</html>