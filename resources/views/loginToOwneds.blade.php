
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta charset="utf-8" />
		<title>Crear contraseña</title>
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
			window.onload = function()
			{
		// fix for windows 8
		if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
			document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="css/windows.chrome.fix.css" />'
		}
		</script>
		<?php  use App\Classes\Mobile;  $mobile = new Mobile(); ?>

		<style type="text/css">
			.push-20{
				margin-bottom: 20px;
			}
		</style>
	</head>
	<body class="fixed-header ">

			<div class="login-wrapper ">
				<!-- START Login Background Pic Wrapper-->
				<div class="bg-pic" >
					<!-- START Background Pic-->

					<img src="/assets/img/terraza-sierra-nevada.jpg" data-src="/assets/img/terraza-sierra-nevada.jpg" data-src-retina="/assets/img/terraza-sierra-nevada.jpg" alt="" class="lazy" style="opacity: 1 !important;width: 100%!important;float: right;">
					<!-- END Background Pic-->
					<!-- START Background Caption-->
					<div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">

					</div>
					<!-- END Background Caption-->
				</div>
				<!-- END Login Background Pic Wrapper-->

				<!-- START Login Right Container-->
				<div class="login-container " style="float: left;background-color: rgba(0,0,0,0.6);">
				<?php if (isset($message)): ?>
					<div class="col-xs-12" style="margin: 20px 0">
						<div class="alert alert-danger">
						  	<strong><?php echo $message[0] ?></strong> <?php echo $message[1] ?>
						</div>
					</div>
				<?php endif ?>
				<div class="col-xs-12">
					<h2 class="semi-bold text-white push-20" >Crear contraseña</h2>

					<!-- START Login Form -->
					<form class="form-horizontal" role="form" method="POST" action="{{ url('admin/propietario/create/password/') }}/<?php echo base64_encode($user) ?>">
						{{ csrf_field() }}

						<div class="col-md-12 push-20">
							<p class="col-md-12 text-white bold" style="color: white;">E-Mail</p>
							<input id="email" type="email" class="form-control" name="email" value="{{ $user }}">
						</div>
						<div class="col-md-12 push-20">
							<p class="col-md-12 text-white bold" style="color: white;">Password</p>
							<input type="password" class="form-control" name="password" />
						</div>
						<div class="col-md-12 push-20">
							<p class="col-md-12 text-white bold" style="color: white;">Repetir password</p>
							<input type="password" class="form-control" name="rep-password" />
						</div>

						<div class="form-group">
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-btn fa-envelope"></i> Crear contraseña
								</button>
							</div>
						</div>
					</form>

					<!--END Login Form-->
				</div>
			</div>
				<!-- END Login Right Container-->

			</div>

		<div class="overlay hide" data-pages="search">
			<!-- BEGIN Overlay Content !-->
			<div class="overlay-content has-results m-t-20">
				<!-- BEGIN Overlay Header !-->
				<div class="container-fluid">
					<!-- BEGIN Overlay Logo !-->
					<img class="overlay-brand" src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png" data-src-retina="assets/img/logo_2x.png" width="78" height="22">
					<!-- END Overlay Logo !-->
					<!-- BEGIN Overlay Close !-->
					<a href="#" class="close-icon-light overlay-close text-black fs-16">
						<i class="fa fa-close"></i>
					</a>
					<!-- END Overlay Close !-->
				</div>
				<!-- END Overlay Header !-->
				<div class="container-fluid">
					<!-- BEGIN Overlay Controls !-->
					<input id="overlay-search" class="no-border overlay-search bg-transparent" placeholder="Search..." autocomplete="off" spellcheck="false">
					<br>
					<div class="inline-block">
						<div class="checkbox right">
							<input id="checkboxn" type="checkbox" value="1" checked="checked">
							<label for="checkboxn"><i class="fa fa-search"></i> Search within page</label>
						</div>
					</div>
					<div class="inline-block m-l-10">
						<p class="fs-13">Press enter to search</p>
					</div>
					<!-- END Overlay Controls !-->
				</div>
				<!-- BEGIN Overlay Search Results, This part is for demo purpose, you can add anything you like !-->
				<div class="container-fluid">
					<span>
						<strong>suggestions :</strong>
					</span>
					<span id="overlay-suggestions"></span>
					<br>
					<div class="search-results m-t-40">
						<p class="bold">Pages Search Results</p>
						<div class="row">
							<div class="col-md-6">
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular bg-success text-white inline m-t-10">
										<div>
											<img width="50" height="50" src="assets/img/profiles/avatar.jpg" data-src="assets/img/profiles/avatar.jpg" data-src-retina="assets/img/profiles/avatar2x.jpg" alt="">
										</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5"><span class="semi-bold result-name">ice cream</span> on pages</h5>
										<p class="hint-text">via john smith</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular bg-success text-white inline m-t-10">
										<div>T</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5"><span class="semi-bold result-name">ice cream</span> related topics</h5>
										<p class="hint-text">via pages</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular bg-success text-white inline m-t-10">
										<div><i class="fa fa-headphones large-text "></i>
										</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5"><span class="semi-bold result-name">ice cream</span> music</h5>
										<p class="hint-text">via pagesmix</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
							</div>
							<div class="col-md-6">
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular bg-info text-white inline m-t-10">
										<div><i class="fa fa-facebook large-text "></i>
										</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5"><span class="semi-bold result-name">ice cream</span> on facebook</h5>
										<p class="hint-text">via facebook</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular bg-complete text-white inline m-t-10">
										<div><i class="fa fa-twitter large-text "></i>
										</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5">Tweats on<span class="semi-bold result-name"> ice cream</span></h5>
										<p class="hint-text">via twitter</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
								<!-- BEGIN Search Result Item !-->
								<div class="">
									<!-- BEGIN Search Result Item Thumbnail !-->
									<div class="thumbnail-wrapper d48 circular text-white bg-danger inline m-t-10">
										<div><i class="fa fa-google-plus large-text "></i>
										</div>
									</div>
									<!-- END Search Result Item Thumbnail !-->
									<div class="p-l-10 inline p-t-5">
										<h5 class="m-b-5">Circles on<span class="semi-bold result-name"> ice cream</span></h5>
										<p class="hint-text">via google plus</p>
									</div>
								</div>
								<!-- END Search Result Item !-->
							</div>
						</div>
					</div>
				</div>
				<!-- END Overlay Search Results !-->
			</div>
			<!-- END Overlay Content !-->
		</div>

		<!-- END OVERLAY -->
		<!-- BEGIN VENDOR JS -->
		<script src="/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/modernizr.custom.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/bootstrapv3/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery-bez/jquery.bez.min.js"></script>
		<script src="/assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery-actual/jquery.actual.min.js"></script>
		<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
		<script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
		<script type="text/javascript" src="/assets/plugins/classie/classie.js"></script>
		<script src="/assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
		<script src="/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
		<!-- END VENDOR JS -->
		<script src="/pages/js/pages.min.js"></script>
		<script>
			$(function()
			{
				$('#form-login').validate()
			})
		</script>
	</body>
</html>