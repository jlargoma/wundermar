<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta charset="utf-8" />
	<title>Recuperar contraseña</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
	<link rel="apple-touch-icon" href="ico/60.png">
	<link rel="apple-touch-icon" sizes="76x76" href="ico/76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="ico/120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="ico/152.png">
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta content="" name="description" />
	<meta content="" name="author" />
	<link href="/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/css/pages-icons.css" rel="stylesheet" type="text/css">
	<link class="main-stylesheet" href="/css/pages.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 9]>
		<link href="css/ie9.css" rel="stylesheet" type="text/css" />
		<![endif]-->
	<script type="text/javascript">
		window.onload = function() {
			// fix for windows 8
			if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
				document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="css/windows.chrome.fix.css" />'
		}
	</script>
	<?php $imglockscreen = asset('img/lockscreen.jpg'); ?>

</head>

<body class="fixed-header ">
	<div class="login-wrapper ">
		<!-- START Login Background Pic Wrapper-->
		<div class="bg-pic">
			<!-- START Background Pic-->

			<img src="{{ $imglockscreen }}" data-src="{{ $imglockscreen }}" data-src-retina="{{ $imglockscreen }}" alt="" class="lazy" style="opacity: 1 !important;min-width: 100%!important;float: right;">
			<!-- END Background Pic-->
		</div>
		<!-- END Login Background Pic Wrapper-->

		<!-- START Login Right Container-->
		<div class="login-container " >
			<div class="p-l-50 m-l-20 p-r-50 m-r-20  m-t-10 sm-p-l-15 sm-p-r-15 sm-p-t-10">
				<h2 class="semi-bold text-white">Recuperar contraseña</h2>

				<!-- START Login Form -->
				<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
					{{ csrf_field() }}

					<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
						<p class="col-md-12 text-white bold" style="color: white;">E-Mail Address</p>

						<div class="col-md-12">
							<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

							@if ($errors->has('email'))
							<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 text-center col-md-offset-4">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-btn fa-envelope"></i> Enviar contraseña nueva
							</button>
						</div>
					</div>
				</form>

				<!--END Login Form-->
			</div>
		</div>
		<!-- END Login Right Container-->
	</div>

	<!-- END OVERLAY -->

</body>

</html>