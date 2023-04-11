<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
      <?php
      $oContents = new App\Contents();
      $meta_descripcion = $oContents->getContentByKey('meta_descripcion');
      $oSEO = $oContents->getSEOContentByPath(Request::path());
      ?>
        <title>@yield('title',$oSEO[0])</title>
        <meta name="description" content="@yield('metadescription',$oSEO[1])">
        
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        
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
	<!-- Stylesheets
	============================================= -->
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ assetV ('/css/frontend.css')}}" type="text/css" />
	<link rel="stylesheet" href="{{ assetV('/frontend/css/responsive.css')}}" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<!-- Document Title
	============================================= -->
	<style type="text/css">
		#contact-form input{
			color: white!important;
		}
		*::-webkit-input-placeholder {
	    /* Google Chrome y Safari */
	    color: rgba(255,255,255,0.85) !important;
		}
		*:-moz-placeholder {
		    /* Firefox anterior a 19 */
		    color: rgba(255,255,255,0.85) !important;
		}
		*::-moz-placeholder {
		    /* Firefox 19 y superior */
		    color: rgba(255,255,255,0.85) !important;
		}
		*:-ms-input-placeholder {
		    /* Internet Explorer 10 y superior */
		    color: rgba(255,255,255,0.85) !important;
		}
	</style>
	@yield('css')
		<script type="text/javascript" src="{{ assetV('/frontend/js/jquery.js') }}"></script>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-66225892-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-66225892-1');
</script>

	
</head>

<body class="stretched no-transition">

	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">
          @yield('content')
	</div><!-- #wrapper end -->

	<!-- Go To Top
	============================================= -->
	<div id="gotoTop" class="icon-angle-up" style="bottom:100px; right:15px;"></div>

	<!-- External JavaScripts
	============================================= -->
	<script type="text/javascript" src="{{ assetV('/js/bootstrap-notify.js')}}"></script>
	<script type="text/javascript" src="{{ assetV('/frontend/js/plugins.js') }}"></script>
	<script type="text/javascript" src="{{ assetV('/frontend/js/functions.js') }}"></script>
	<?php /* view para todos los scripts generales de la pagina*/ ?>
	@yield('scripts')
</body>
</html>