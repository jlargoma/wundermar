<!DOCTYPE html>
<html>
    <head>

        <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
        <meta charset="utf-8"/>
        <title>wundermar - @yield('title')</title>
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"/>
        <meta name="TWpUeE0zeDhNZkQ2STF3ZU1mVHhjcT0y" value="1003272df3af89e0ab299138ff66db15"/>
        <link rel="apple-touch-icon" href="pages/ico/60.png">
        <link rel="apple-touch-icon" sizes="76x76" href="pages/ico/76.png">
        <link rel="apple-touch-icon" sizes="120x120" href="pages/ico/120.png">
        <link rel="apple-touch-icon" sizes="152x152" href="pages/ico/152.png">
        <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <link href="{{ assetV('/assets/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ assetV('/assets/plugins/bootstrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css"
              integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous">
        <link href="{{ assetV('/assets/plugins/jquery-scrollbar/jquery.scrollbar.css') }}" rel="stylesheet" type="text/css"
              media="screen"/>
        <link href="{{ assetV('/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"
              media="screen"/>
        <link href="{{ assetV('/assets/plugins/switchery/css/switchery.min.css') }}" rel="stylesheet" type="text/css"
              media="screen"/>
        <link href="{{ assetV('/assets/plugins/nvd3/nv.d3.min.css') }}" rel="stylesheet" type="text/css" media="screen"/>
        <link href="{{ assetV('/assets/plugins/mapplic/css/mapplic.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ assetV('/assets/plugins/rickshaw/rickshaw.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ assetV('/assets/plugins/bootstrap-datepicker/css/datepicker3.css') }}" rel="stylesheet"
              type="text/css" media="screen">
        <link href="{{ assetV('/assets/plugins/jquery-metrojs/MetroJs.css') }}" rel="stylesheet" type="text/css"
              media="screen"/>
        <link href="{{ assetV('/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
        <link class="main-stylesheet" href="{{ assetV('/css/pages.css') }}" rel="stylesheet" type="text/css"/>
        <script src="//code.jquery.com/jquery.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
              integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        @yield('externalScripts')
           <!--[if lte IE 9]>
        <link href="/assets/plugins/codrops-dialogFx/dialog.ie.css" rel="stylesheet" type="text/css" media="screen"/>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="{{ assetV('/css/custom.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ assetV('/css/custom-backend.css')}}">

        <script src="https://kit.fontawesome.com/922ed7fba7.js" crossorigin="anonymous"></script>
            <style>
        <?php 
        
        if (config('app.appl') == "wundermar"): ?>
               .showOnlywundermar{
                 visibility:visible;
               }
               .hiddenOnlywundermar{
                visibility:hidden;
                display: none !important;
               }
            <?php else:?>
              .showOnlywundermar{
                 visibility:hidden;
               }
            <?php endif; ?>
        </style>
    </head>
    <body class="fixed-header   windows desktop pace-done sidebar-visible menu-pin" style="padding-top:0px!important">
            <nav class="navbar navbar-inverse" role="navigation" style="<?php if (config('app.appl') == "wundermar"): ?>background-color: #6d5cae!important; <?php else:?> background-color: #295d9b!important;<?php endif; ?>">
                <a class="navbar-brand" href="{{ route('dashboard.planning') }}" style="max-width: 155px;">
                    <img src="{{ assetV('img/logo.png') }}" alt="" style="width: 100%">
                </a>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" style="<?php if (config('app.appl') == "wundermar"): ?>background-color: #6d5cae!important; <?php else:?> background-color: #295d9b!important;<?php endif; ?>">
               @include('layouts._nav_links')
                <ul class="nav navbar-nav navbar-right">
                    <li style="color:white"><a href="#"
                                               style="pointer-events: none"><?php echo ucwords(Auth::user()->name) ?></a></li>
                    <li><a href="{{url ('/logout')}}">Log Out</a></li>
                </ul>
            </div>
            <?php if (stristr(Request::path(), 'liquidacion') == TRUE || Request::path() == 'admin/liquidacion-apartamentos' || Request::path() == 'admin/pagos-propietarios' || Request::path() == 'admin/pagos-estadisticas' || Request::path() == 'admin/perdidas-ganancias' ): ?>
                <div class="navbar-collapse collapse">
                    @yield('headerButtoms')
                </div>
            <?php endif ?>

        </nav>
        <div class="page-container ">
            <div class="page-content-wrapper ">
                  @if($errors->any())
                  <p class="alert alert-danger">{{$errors->first()}}</p>
                  @endif
                  @if (\Session::has('success'))
                  <p class="alert alert-success">{!! \Session::get('success') !!}</p>
                  @endif
                <div class="content sm-gutter " style="padding-left: 0px!important;padding-top: 0px!important;">
                    @yield('content')
                </div>
                <!-- END CONTENT -->
            </div>
        </div>
        <!-- END CONTAINER FLUID -->
        </div>
        <div class="overlay loading-box" id="loadigPage">
          <div >
            <h3 class="text-center"><i class="fas fa-spinner fa-spin"></i>Loading...</h3>
          </div>
        </div>
        <div class="overlay message-bottom-box" id="bottom_msg">
          <div id="bottom_msg_text">
          </div>
        </div>
        <!-- BEGIN VENDOR JS -->
        <script src="{{ assetV('assets/plugins/pace/pace.min.js') }}" type="text/javascript"></script>

        <script src="{{ assetV('assets/plugins/modernizr.custom.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/bootstrapv3/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery/jquery-easy.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery-bez/jquery.bez.min.js') }}"></script>
        <script src="{{ assetV('assets/plugins/jquery-ios-list/jquery.ioslist.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery-actual/jquery.actual.min.js') }}"></script>
        <script src="{{ assetV('assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script type="text/javascript" src="{{ assetV('assets/plugins/select2/js/select2.full.min.js') }}"></script>
        <script type="text/javascript" src="{{ assetV('assets/plugins/classie/classie.js') }}"></script>
        <script src="{{ assetV('assets/plugins/switchery/js/switchery.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/lib/d3.v3.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/nv.d3.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/utils.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/tooltip.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/interactiveLayer.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/models/axis.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/models/line.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/nvd3/src/models/lineWithFocusChart.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/mapplic/js/hammer.js') }}"></script>
        <script src="{{ assetV('assets/plugins/mapplic/js/jquery.mousewheel.js') }}"></script>
        <script src="{{ assetV('assets/plugins/mapplic/js/mapplic.js') }}"></script>
        <script src="{{ assetV('assets/plugins/rickshaw/rickshaw.min.js') }}"></script>
        <script src="{{ assetV('assets/plugins/jquery-metrojs/MetroJs.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/skycons/skycons.js') }}" type="text/javascript"></script>
        <script src="{{ assetV('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
        <script type="text/javascript" src="{{ assetV('/js/bootstrap-notify.js')}}"></script>

        <script src="{{ assetV('js/pages.min.js') }}"></script>
        <script src="{{ assetV('js/custom.js') }}"></script>
        @yield('scripts')
        
       
    </body>
</html>
