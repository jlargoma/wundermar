<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>
<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/plugins/bootstrap-datepicker/css/datepicker3.css')}}" type="text/css" >
<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css">


<div class="row">
    <h5 class="text-center">Seleccione un Rango</h5>
    <form role="form" action="{{ url('/admin/temporadas/update/'.$season->id) }}" method="post">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id" value="<?php echo $season->id; ?>">
        <div class="col-md-6 col-xs-12">
	        <?php
	        $start1 = Carbon::createFromFormat('Y-m-d', $season->start_date)->format('d M, y');
	        // $start1 = str_replace('Apr','Abr',$start->format('d M, y'));
	        $finish1 = Carbon::createFromFormat('Y-m-d', $season->finish_date)->format('d M, y');
	        // $finish1 = str_replace('Apr','Abr',$finish->format('d M, y'));
	        ?>
            <input type="text" class="form-control daterange1" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;"
            readonly="" placeholder="Seleccione sus fechas" value="<?php echo $start1 ;?> - <?php echo $finish1 ?>">
        </div>
        <div class="col-md-3 col-xs-12">
            <select class="form-control full-width" name="type">
		        <?php foreach (\App\TypeSeasons::orderBy('order', 'ASC')->get() as $typeSeason): ?>
                <option value="<?php echo $typeSeason->id ?>" <?php if($typeSeason->id == $season->type): ?> selected <?php
                endif?>>
                    <?php echo $typeSeason->name ?>
                </option>
		        <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3 col-xs-12">
            <button class="btn btn-complete" type="submit">Actualizar</button>
        </div>
    </form>

</div>


<script src="/assets/plugins/moment/moment.min.js"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>

<script type="text/javascript">
  $(document).ready(function () {
    $(function() {
      $(".daterange1").daterangepicker({
        "buttonClasses": "button button-rounded button-mini nomargin",
        "applyClass": "button-color",
        "cancelClass": "button-light",
        locale: {
          format: 'DD MMM, YY',
          "applyLabel": "Aplicar",
          "cancelLabel": "Cancelar",
          "fromLabel": "From",
          "toLabel": "To",
          "customRangeLabel": "Custom",
          "daysOfWeek": [
            "Do",
            "Lu",
            "Mar",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
          ],
          "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
          ],
          "firstDay": 1,
        },

      });
    });
  });
</script>