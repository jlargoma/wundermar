<div style="margin-top: 10px;margin-left: 10px;">
	<?php $direccion = explode("/", Request::path()) ?>
	<?php if ($direccion[1] == "liquidacion"): ?>
		<a class="text-white btn btn-sm btn-success font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;" disabled>
		    Liquidacion
	    </a>
	<?php else: ?>
		<a href="{{url('admin/liquidacion')}}" class="text-white btn btn-sm btn-primary font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;">
		    Liquidacion
	    </a>
	<?php endif ?>

	<?php if (Request::path() == "admin/liquidacion-apartamentos"): ?>
		<a class="text-white btn btn-sm btn-success font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;" disabled>
		    Liquidacion de Apartamentos
	    </a>
	<?php else: ?>
		<a href="{{url('admin/liquidacion-apartamentos')}}" class="text-white btn btn-sm btn-primary font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;">
		    Liquidacion de Apartamentos
	    </a>
	<?php endif ?>

	<?php if (Request::path() == "admin/pagos-propietarios"): ?>
		<a class="text-white btn btn-sm btn-success font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;" disabled>
		    Pagos a propietarios
	    </a>
	<?php else: ?>
		<a href="{{url('admin/pagos-propietarios')}}" class="text-white btn btn-sm btn-primary font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;">
		    Pagos a propietarios
	    </a>
	<?php endif ?>

	<?php if (Request::path() == "admin/estadisticas"): ?>
		<a class="text-white btn btn-sm btn-success font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;" disabled>
		    Estadisticas
	    </a>
	<?php else: ?>
		<a href="{{url('admin/estadisticas')}}" class="text-white btn btn-sm btn-primary font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;">
		    Estadisticas
	    </a>
	<?php endif ?>
	
	<?php if (Request::path() == "admin/perdidas-ganancias"): ?>
		<a class="text-white btn btn-sm btn-success font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;" disabled>
		    P & G
	    </a>
	<?php else: ?>
		<a href="{{url('admin/perdidas-ganancias')}}" class="text-white btn btn-sm btn-primary font-s16 font-w300" style="padding: 10px 15px;line-height: 15px;">
		    P & G
	    </a>
	<?php endif ?>
</div>