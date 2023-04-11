<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
  <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>

<div style="max-width: 480px;margin: auto;padding: 1em;">

  <h4 class="text-center">Bloqueo de Apartamentos</h4>
  <div class="row">
    <div class="col-md-4 col-xs-12">
      <label>Edificio</label>
      <select id="mrlSite" class="form-control">
        <option value="-1"></option>
        <?php foreach ($sites as $k => $v): ?>
          <option value="<?php echo $k; ?>" ><?php echo $v; ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-md-4 col-xs-12" >
      <label>Fechas</label>
      <div class="input-prepend input-group input_dates" style="width: 100%">
        <input type="text" class="form-control daterange02 minimal" id="mrlFechas"  value="<?php echo $start->format('d M, y'); ?> - <?php echo $finish->format('d M, y') ?>">
        <input type="hidden" class="date_start" id="start" name="mrlStart" value="{{$start->format('Y-m-d')}}">
        <input type="hidden" class="date_finish" id="finish" name="mrlFinish" value="{{$finish->format('Y-m-d')}}">
      </div>
    </div>
    <div class="col-md-4 col-xs-12">
      <button type="button" class="btn btn-danger" style="margin-top: 24px;" id="mrlSend">Bloquear</button>
    </div>
  </div>
  <hr>
  <h4 class="text-center mt-1em">Bloqueo Automático</h4>  

  <div class="form-group row">
    <label for="static" class="col-sm-5 col-xs-6 col-form-label">Hora cierre automático</label>
    <div class="col-sm-4 col-xs-6">
      <select id="mrlTime" class="form-control">
        <option value="-1"></option>
        <?php for ($i = 0; $i < 24; $i++): ?>
          <option value="<?php echo $i; ?>" <?php echo ($aTaskData['time'] == $i) ? 'selected' : ''; ?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
        <?php endfor ?>
      </select>
    </div>
  </div>
  <div class="form-group row">
    <label for="static" class="col-sm-5 col-form-label">Edificio</label>
    <div class="col-sm-7">
      <?php foreach ($sites as $k => $v): ?>
        <?php $checked = $aTaskData['sites'][$k] ? 'checked' : ''; ?>
        <div class="form-check">
          <input type="checkbox" class="form-check-input checkSites" data-id="<?php echo $k; ?>" <?php echo $checked; ?>>
          <label class="form-check-label" for="exampleCheck1">{{$v}}</label>
        </div>
      <?php endforeach ?>
    </div>
  </div>
  <button type="button" class="btn btn-danger mt-1em" id="mrlSendProgr">Guardar</button>
</div>

<script type="text/javascript">
  $(document).ready(function () {

    $(".daterange02").daterangepicker({
      "buttonClasses": "button button-rounded button-mini nomargin",
      "applyClass": "button-color",
      "cancelClass": "button-light",
      autoUpdateInput: true,
      locale: {
        firstDay: 1,
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
      },

    });

    Date.prototype.yyyymmmdd = function () {
      var mm = this.getMonth() + 1; // getMonth() is zero-based
      var dd = this.getDate();
      return [
        this.getFullYear(),
        (mm > 9 ? '' : '0') + mm,
        (dd > 9 ? '' : '0') + dd
      ].join('-');
    };


    $('#mrlSendProgr').on('click', function () {
      var sites = new Array();
              
      $( ".checkSites" ).each(function( index ) {
        if ($( this ).prop('checked')){
          sites.push($( this ).data('id'));
        }
      });
      
      var data = {
        sites: sites,
        time: $("#mrlTime").val(),
        _token: "{{csrf_token()}}"
      }

      $.post('/admin/multiple-room-lock-task', data, function (resp) {
        window.show_notif(resp.title, resp.status, resp.msg);
      });
    });
    
    
    $('#mrlSend').on('click', function () {
      var date = $('#mrlFechas').val();
      var arrayDates = date.split('-');
      var res1 = arrayDates[0].replace("Abr", "Apr");
      var date1 = new Date(res1);
      var start = date1.getTime();

      var res2 = arrayDates[1].replace("Abr", "Apr");
      var date2 = new Date(res2);

      var data = {
        site: $("#mrlSite").val(),
        start: date1.yyyymmmdd(),
        finish: date2.yyyymmmdd(),
        _token: "{{csrf_token()}}"
      }

//      if (data.site < 1) {
//        alert('seleccione un edificio para bloquear');
//        return null;
//      }
      $.post('/admin/multiple-room-lock', data, function (resp) {
        window.show_notif(resp.title, resp.status, resp.msg);
      });
    });

  });
</script>

