<!DOCTYPE html>
<html dir="ltr" lang="es-ES">
    <head>
      <?php noIndex(); ?>
      <?php
      $oContents = new App\Contents();
      $meta_descripcion = $oContents->getContentByKey('meta_descripcion');
      $roomsUrl = App\RoomsType::getMenuRooms();
      $site_id = config('app.site_id',1);
      $oSEO = $oContents->getSEOContentByPath(Request::path());
      ?>
        <title>@yield('title',$oSEO[0])</title>
        <meta name="description" content="@yield('metadescription',$oSEO[1])">
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="keywords" content="Alquiler apartamento Sierra Nevada;edificio miramarski; a pie de pista; apartamentos capacidad 6 / 8 personas; estudios capacidad 4 /5 personas; zona baja;piscina climatizada;gimansio;parking cubierto; a 5 minutos  de la plaza de Andalucia">
        

        <link rel="apple-touch-icon" sizes="57x57" href="{{ assetV('/img/miramarski/favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ assetV('/img/miramarski/favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ assetV('/img/miramarski/favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ assetV('/img/miramarski/favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ assetV('/img/miramarski/favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ assetV('/img/miramarski/favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ assetV('/img/miramarski/favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ assetV('/img/miramarski/favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ assetV('/img/miramarski/favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ assetV('/img/miramarski/favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ assetV('/img/miramarski/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ assetV('/img/miramarski/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ assetV('/img/miramarski/favicon/favicon-16x16.png') }}">
        <!--<link rel="manifest" href="/manifest.json">-->
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ assetV('/img/miramarski/favicon/ms-icon-144x144.png' ) }}">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="{{ getCloudfl(assetV ('/css/frontend.css'))}}" type="text/css"/>   
        
        @yield('css')

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-66225892-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-66225892-1');
</script>

</head>

<body class="stretched not-dark" data-loader="5">

    <div class="clearfix">

        @include('layouts._header')
        @yield('content')
        @include('layouts._footer')
    </div>
    <div id="gotoTop" class="fa fa-chevron-up" style="bottom:100px; right:15px;"></div>
 
        
    <script  src="https://code.jquery.com/jquery-2.1.4.min.js"
  integrity="sha256-8WqyJLuWKRBVhxXIL1jBDD7SDxU936oZkCnxQbWwJVw="
  crossorigin="anonymous"></script>
  
   
    @include('layouts._generalScriptsFront')
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
</html>
