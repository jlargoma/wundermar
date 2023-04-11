@extends('layouts.master')

@section('title')Pagos apartamentosierranevada.net @endsection

@section('content')
<style type="text/css">
	#primary-menu ul li  a{
		color: #3F51B5!important;
	}
	#primary-menu ul li  a div{
		text-align: left!important;
	}
	label{
		color: white!important
	}
	#content-form-book {
    	padding: 40px 15px;
	}
	@media (max-width: 768px){
		

		.container-mobile{
			padding: 0!important
		}
		#primary-menu{
			padding: 40px 15px 0 15px;
		}
		#primary-menu-trigger {
		    color: #3F51B5!important;
		    top: 5px!important;
		    left: 5px!important;
		    border: 2px solid #3F51B5!important;
		}
		.container-image-box img{
			height: 180px!important;
		}

		#content-form-book {
			padding: 0px 0 40px 0
		}
		.daterangepicker {
		    top: 135%!important;
		}
		.img{
			max-height: 530px;
		}
		.button.button-desc.button-3d{
			background-color: #4cb53f!important;
		}

	}
</style>
<style type="text/css" media="screen">
    .shadow{
        text-shadow: 1px 1px #000;
    }
    .stripe-button-el{
        background-image: linear-gradient(#28a0e5,#015e94);
        color: #FFF;
        width: 100%;
        padding: 30px 15px;
        font-size: 24px;

    }
    .stripe-button-el span{
        background: none;
        color: #FFF;
        height: auto!important;
        padding: 0;
        font-size: 24px;
        font-family: 'Evolutio',sans-serif;
        letter-spacing: -2px;
        line-height: inherit;
        text-shadow: none;
        -webkit-box-shadow: none; 
        box-shadow: none;
    }
    footer#footer{
    	margin-top: 0!important
    }
</style>
<?php if (!$mobile->isMobile()): ?>
	<section class="section nobottommargin" style="background-image: url({{ asset('/img/mountain.png')}});background-repeat: no-repeat;background-size: 100%;background-position: 0;padding: 0;margin: 0;min-height: 564px;" >
		<div class="vertical-middle">
			<div class="container container-mobile clearfix" style="width: 85%;">

				<div class="col-md-8 col-md-offset-2">
					<div class="col-md-12">
						<h2 class=" text-center font-w300 ls1 shadow" style="line-height: 1; font-size: 42px;">
							<?php echo $message[0] ?><br>
							<span class="font-w800 black shadow" style="font-size: 56px;letter-spacing: -3px;">
								<?php echo $message[1] ?><br>
							</span>
							
						</h2>
						<?php if (isset($message[2]) && !empty($message[2])): ?>
							<p class="text-center">Te estamos redirigiendo a las reservas, por favor espera</p>
						<?php endif ?>
					</div>							
				</div>
			</div>
		</div>
					
	</section>
<?php else:?>
	<section class="section nobottommargin" style="background-image: url({{ asset('/img/mountain.png')}});background-repeat: no-repeat;background-size: cover; background-position: inherit; padding: 0;" >
		<div class="container container-mobile clearfix" style="width: 85%;    min-height: 450px;">
			<div class="col-xs-12 nobottommargin">
				
				<div class="row">
					
					<div class="col-md-12">
						<h2 class="text-center font-w300 ls1 shadow" style="line-height: 1; font-size: 24px;">
							<?php echo $message[0] ?><br>
							<span class="font-w800 shadow" style="font-size: 32px; letter-spacing: -3px;">
								<?php echo $message[1] ?><br>
							</span>
							
						</h2>
						<?php if (isset($message[2]) && !empty($message[2])): ?>
							<p class="text-center">Te estamos redirigiendo a las reservas, por favor espera</p>
						<?php endif ?>
					</div>
					
				</div>
				
			</div>
		</div>
	</section>
<?php endif ;?>
<script type="text/javascript">
	// setTimeout(function(){
	//   window.location.href = "/admin/reservas";
	// }, 4000);
</script>
@endsection