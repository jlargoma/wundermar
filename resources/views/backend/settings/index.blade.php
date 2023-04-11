<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.admin-master')

@section('title') Configuración @endsection

@section('externalScripts')
    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css"
          media="screen"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 setting">
  <h1 class="font-w800">CONFIGURACIONES</h1>
  <div class="row">
    <!-- COLUMN 1 -->
    <div class="col-md-4">
      @include('backend.settings.blocks.agentes')
    </div>
    <div class="col-md-4">
      @include('backend.settings.blocks.general-settings')
    </div>
    <div class="col-md-4">
      @include('backend.settings.blocks.stripe-pagos')
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      @include('backend.settings.blocks.longs-settings')
    </div>
    <div class="col-md-8">
      @include('backend.settings.blocks.sites')
    </div>
  </div>

    
    <div class="modal fade slide-up in" id="agentRoom" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content-wrapper">
                <div class="modal-content">
                    <div class="block">
                        <div class="block-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                        class="pg-close fs-14"
                                        style="font-size: 40px!important;color: black!important"></i>
                            </button>
                            <h2 class="text-center">
                                Agentes - Aparts
                            </h2>
                        </div>
                        <div class="block block-content" id="contentAgentRoom" style="padding:20px">
                            <div class="row">
                                <form action="{{ url('/admin/agentRoom/create') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
									<?php $roomsToAgents = \App\Rooms::where('state', '=', 1)->orderBy('order')
									                                 ->get();?>
									<?php $agents = \App\User::where('role', 'agente')->get();?>
                                    <div class="col-md-3 col-xs-12 push-10">
                                        <label>Agente</label>
                                        <select class="form-control full-width minimal" name="user_id" required>
                                            <option></option>
											<?php foreach ($agents as $agent): ?>
                                            <option value="<?php echo $agent->id ?>">
												<?php echo $agent->name  ?>
                                            </option>
											<?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-xs-12 push-10">
                                        <label>Apartamento</label>
                                        <select class="form-control full-width minimal" name="room_id" required>
                                            <option></option>
											<?php foreach ($roomsToAgents as $room): ?>
                                            <option value="<?php echo $room->id ?>">
												<?php echo substr($room->nameRoom . " - " . $room->name, 0, 12)  ?>
                                            </option>
											<?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-xs-12 push-10">
                                        <label>Agencia</label>
                                        <select class="form-control full-width agency minimal" name="agency_id">
                                           <?php $book = new \App\Book(); ?>
                                            @include('backend.blocks._select-agency', ['agencyID'=>0,'book' => $book])
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-xs-12 push-10">
                                        <br>
                                        <button class="btn btn-complete font-w400" type="submit">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<div class="container-fluid padding-25 sm-padding-10">
<div class="row">
  <div class="col-md-3 col-xs-6">
    
  </div>
</div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
    <script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
    <script src="{{ asset('/assets/js/notifications.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        function showNotify(status, message, type){
          $.notify({
            title: '<strong>'+status+'</strong>, ',
            icon: 'glyphicon glyphicon-star',
            message: message
          },{
            type: type,
            animate: {
              enter: 'animated fadeInUp',
              exit: 'animated fadeOutRight'
            },
            placement: {
              from: "top",
              align: "right"
            },
            allow_dismiss: false,
            offset: 80,
            spacing: 10,
            z_index: 1031,
            delay: 500,
            timer: 500,
          });
        }
        
      $(document).ready(function () {
      
        
        var sendWeiland = function(id){
          var percent = $('#w_percent_' + id).val();
          var numDays = $('#w_days_' + id).val();
          var fianza = 0;
          if($('#w_fianza_' + id).is(':checked')) fianza = 1;

          $.get(
              "{{route('settings.weiland.upd')}}", 
              {id: id, percent: percent, numDays: numDays, fianza: fianza},
              function (data) {

                if (data == 'ok'){
                  window.show_notif('OK','success','Registro Weiland Actualizado.');
//                  setTimeout(function(){window.location.reload();},700);

                } else{
                  window.show_notif('ERROR','danger','Weiland no encontrado');
                }
              }
            );
        };
        
        $('.Weiland.checked').on('change',function(e) {
          var id = $(this).attr('data-id');
              sendWeiland(id);
        })
        $('.Weiland').on('keypress',function(e) {
            if(e.which == 13) {
              var id = $(this).attr('data-id');
              sendWeiland(id);
            }
        });
        

        

        

        $(".monthRange").change(function () {
          var yearMonths = $(this).val();
          console.log(yearMonths);
//          $.post("{{ route('years.change.month') }}", {dates: yearMonths}).done(function (data) {
//            console.log(data);
//            location.reload();
//          });
        });

        $('.setting-editable').change(function () {
          var code = $(this).attr('data-code');
          var value = $(this).val();
          $.post("{{ route('settings.createUpdate') }}", {code: code, value: value}).done(function (data) {
            var response = jQuery.parseJSON(data);
            showNotify(response.status, response.message, 'success')
          }).fail(function (data) {
            var response = jQuery.parseJSON(data);
            showNotify(response.status, response.message, 'danger')
          });
        });
      });
    </script>
@endsection