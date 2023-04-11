<!DOCTYPE html>
<html>
    <head>

        <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
        <meta charset="utf-8"/>
        <title>@yield('title')</title>
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
        <link href="{{ assetV('/assets/plugins/bootstrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css"
              integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous">
        <link class="main-stylesheet" href="{{ assetV('/pages/css/pages.css') }}" rel="stylesheet" type="text/css"/>
        <script src="//code.jquery.com/jquery.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
              integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        @yield('externalScripts')
           <!--[if lte IE 9]>
        <link href="/assets/plugins/codrops-dialogFx/dialog.ie.css" rel="stylesheet" type="text/css" media="screen"/>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="{{ assetV('/pages/css/custom.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ assetV('/css/custom-backend.css')}}">

        <?php
        use App\Classes\Mobile;
        $mobile = new Mobile();
        ?>
    </head>
    <body class="fixed-header   windows desktop pace-done sidebar-visible menu-pin" style="padding-top:0px!important">
        <div class="page-container ">
            <div class="page-content-wrapper ">

                <div class="content sm-gutter " style="padding-left: 0px!important;padding-top: 0px!important;">
                    @yield('content')
                </div>
                <!-- END CONTENT -->
            </div>
        </div>
         <script type="text/javascript" src="{{ assetV('/pages/js/bootstrap-notify.js')}}"></script>
        <script src="{{ assetV('js/custom.js') }}"></script>
        @yield('scripts')
    </body>
</html>
