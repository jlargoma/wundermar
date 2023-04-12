@extends('layouts.admin-master')

<?php 
$isMobile = config('app.is_mobile');
?>
@section('title') Precios de apartamentos @endsection

@section('externalScripts') 

    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
@endsection

@section('content')

<style>
    .Alta{
        background: #f0513c;
    }
    .Media{
        background-color: #127bbd;
    }
    .Baja{
        background-color: #91b85d;
    }

    .Premium{
        background-color: #ff00b1;
        color: white;
    }

    span.Alta{
        background-color: transparent!important;
        color: #f0513c;
        text-transform: uppercase;
    }
    span.Media{
        background-color: transparent!important;
        color: #127bbd;
        text-transform: uppercase;
    }
    span.Baja{
        background-color: transparent!important;
        color: #91b85d;
        text-transform: uppercase;
    }
    span.Premium{
        background-color: transparent!important;
        color: #ff00b1;
        text-transform: uppercase;
    }
    .extras{
        background-color: rgb(150,150,150);
    }
    .btn-inline{
      float:right; margin: 0 5px
    }
    .fa fa-close{
      font-size: 40px!important;color: black!important
    }
    input.datepicker2 {
        padding: 6px;
        border: 1px solid #000;
        text-align: center;
        color: #000;
    }
    i.fa.fa-trash.deleteSegment {
        color: red;
        font-size: 11px;
        cursor: pointer;
    }
    
    .weekdays:first-child {
    margin-left: 10px;
  }
  .weekdays {
    float: left;
    width: 12%;
  }
    .weekdays label {
    display: block;
    padding-left: 10px;
    text-indent: -17px;
    color:#a7a7a7;
  }

  .weekdays input {
    width: 1px;
    height: 1px;
    padding: 0;
    margin: 0;
    vertical-align: bottom;
    position: relative;
    top: -1px;
  }
  
  input:checked + span {
    color: #2b5d9b;
  }
  
  .btn_days {
    padding: 4px 15px;
  }
  .btn_days.active {
    background-color: #2b5d9b;
    color: #fff;
  }
  .table-logs tr td{
    text-align: center;
    padding: 3px 5px !important;
  }
  .table-logs tr th{
    white-space: nowrap;
    font-weight: bold;
    text-align: center;
  }
  @media only screen and (max-width: 425px) {
    .select-site{
      clear: both;
      float: none;
      padding-top: 1em;
    }
    .buttons-lst{
      width: 42em;
    }
    .weekdays {
      width: 13%;
    }
    form label {
      font-size: 11px;
    }
    form.inline-block {
      width: 44%;
      margin: 1%;
      overflow: auto;
    }
  }
</style>

<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3 col-xs-12">
          <div class="row show-mobile">
            <div class="col-xs-8">
              <h2>Precios de Temporadas:</h2>
            </div>
            <div class="col-xs-4 mt-1em">
              @if($isMobile)  @include('backend.years._selector', ['minimal' => true]) @endif
            </div>
          </div>
          <h3 class="hidden-mobile">Precios de Temporadas:</h3>
        </div>
        <div class="col-xs-12 col-md-8">
          @include('backend.prices._navs')
        </div>
        <div class="col-md-1">
            @if(!$isMobile) @include('backend.years._selector', ['minimal' => true]) @endif
        </div>
      </div>
    </div>
  </div>
  
  @if (Auth::user()->email == "jlargo@mksport.es")
  <div class="col-md-12">
    <form action="{{route('precios.prepare-cron')}}" method="post" class="inline-block">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <button class="btn btn-success" title="{{$sendDataInfo}}">Sincr. precios a OTAs</button>
    </form>
    <form action="{{route('precios.prepare-cron-minStay')}}" method="post" class="inline-block">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <button class="btn btn-success" title="{{$sendDataInfo_minStay}}">Sincr Mín Estadías a OTAs</button>
    </form>
    <form action="{{route('channel.sendAvail',['allSeasson'])}}" method="post" class="inline">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <button class="btn btn-success" title="{{$sendDataInfo_minStay}}">Sincr. Disponibilidad OTAs</button>
    </form>
    <small>Enviar todos los precios ó todas las estancias mínimas de la temporada a todas las OTAS </small>
  </div>
  @endif
  
    

    <div class="row">
      <div class="col-md-6">
        @include('backend.prices.blocks.costes')
      </div>
      <div class="col-md-6">
        @include('backend.prices.blocks.extr-paxs')
        @include('backend.prices.blocks.dias-min')
      </div>
    </div>
  </div>

  @include('backend.prices.blocks._modals')

@endsection

@section('scripts')
  <script type="text/javascript" src="/js/datePicker01.js"></script>
  <script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
  <script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
  <script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
  <script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
  <script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>

  <script type="text/javascript">
      $(document).ready(function() {

          $('.new-prices').click(function(event) {
              $.get('/admin/precios/new', function(data) {
                  $('#content-prices').empty().append(data);
              });
          });
          $('.new-special-prices').click(function(event) {
              $.get('/admin/precios/newSpecial', function(data) {
                  $('#content-prices').empty().append(data);
              });
          });

          $('.editable').change(function(event) {
              var id = $(this).attr('data-id');               
              var price = $('.price-'+id).val();
              var cost  = $('.cost-'+id).val();

              $.get('precios/update', {  id: id, price: price,cost: cost}, function(resp) {

                if (resp == 'OK') {
                  window.show_notif('Registro modificado','success','');
                } else {
                  window.show_notif(resp,'danger','');
                }

                  // alert(data);
//                    window.location.reload();
              });

          });
        $('.updateSeason').click(function (event) {
          var id = $(this).attr('data-id');
          $.get('/admin/temporadas/update/' + id, function (data) {
            $('#contentSeason').empty().append(data);
          });
        });


/********************************************************/
    $(".datepicker2").datepicker();

    $('#defineSeason').on('submit',function(event){
      event.preventDefault();
       $.post("{{ route('years.change.month') }}", $(this).serialize()).done(function (resp) {
         if (resp == 'OK') {
          window.show_notif('Registro modificado','success','');
        } else {
          window.show_notif(resp,'danger','');
        }
      });
    });

    $(".s_years").on('change',function() {
      $.get("{{ route('years.get') }}", {id: $(this).val()}).done(function (resp) {
        $('#year_start').val(resp[0]);
        $('#year_end').val(resp[1]);
      });
    });
      
      
      /********************************************************/
      $('.extra-editable').change(function (event) {
          var id = $(this).attr('data-id');
          var price = $('.extra-price-' + id).val();
          var cost = $('.extra-cost-' + id).val();
          var apto = $('#extra-apto-' + id).val();
          var type = $('.extra-type-' + id).val();

          $.get("{{route('settings.extr_price.upd')}}", 
          {id: id, price: price, cost: cost, apto: apto,type:type},
          function (data) {
            
            if (data == 'ok'){
              window.show_notif('OK','success','Registro Actualizado.');
            } else{
              window.show_notif('ERROR','danger','Extra no encontrado');
            }
          });

        });

        $('.deleteSegment').click(function (event) {
          
          if (confirm('Eliminar el Extra '+$(this).data('name')+'?')){
            var data = {
              id: $(this).data('id'),
              _token: "{{csrf_token()}}"
            };
            
            var elemet = $(this).closest('tr');
            
            $.ajax({
                url: "{{route('settings.extr_price.del')}}",
                data: data,
                type: 'DELETE',
                success: function(result) {
                  if (result == 'OK'){
                    window.show_notif('OK','success','Registro Eliminado.');
                    elemet.remove(); 
                  } else{
                    window.show_notif('ERROR','danger','Registro no encontrado');
                  }
                },
                error: function(e){
                  console.log(e);
                  window.show_notif('ERROR','danger','Error de sistema');
                }
            });
          }
        });
      /********************************************************/
    $('.updateSegment').click(function (event) {
          var id = $(this).attr('data-id');
          $.get('/admin/specialSegments/update/' + id, function (data) {
            $('#contentSegments').empty().append(data);
          });
        });  
        
  /**********************************************************************/
  /*****  Min Stay          */
  var allDayW = function(){
    $('.btn_days').removeClass('active');
    for(i=0; i<7; i++){
      $('#dw_'+i).prop("checked", true);
    }
  }
  $('#selAllDays').on('click', function (e) {
    $('.btn_days').removeClass('active');
    $(this).addClass('active');
    allDayW();
  });
  $('#selWorkdays').on('click', function (e) {
     $('.btn_days').removeClass('active');
    $(this).addClass('active');
    allDayW();
    $('#dw_5').prop("checked", false);
    $('#dw_6').prop("checked", false);
  });
   $('#selHolidays').on('click', function (e) {
      $('.btn_days').removeClass('active');
    $(this).addClass('active');
    for(i=0; i<7; i++){
      $('#dw_'+i).prop("checked", false);
    }
    $('#dw_5').prop("checked", true);
    $('#dw_6').prop("checked", true);
  });

  $('#channelFormMinStay').on('submit', function (event) {

    event.preventDefault();
    $('#error').text('').hide();
    $('#success').text('').hide();
    var form_data = $(this).serialize();
    var url = $(this).attr('action');
    $('#success').text('Enviando datos. Por favor, espere a terminar la acción...').show();
    $.ajax({
      type: "POST",
      url: url,
      data: form_data, // serializes the form's elements.
      success: function (data)
      {
        $('#success').text('').hide();
        if (data.status == 'OK') {
          $('#success').text(data.msg).show();
        } else {
          $('#error').text(data.msg).show();
        }
      }
    });
  });
  
  /***********************************************************************/
  
    $('.daterange1').change(function (event) {
      var date = $(this).val();

      var arrayDates = date.split(' - ');
      var res1       = arrayDates[0].replace("Abr", "Apr");
      var date1      = new Date(res1);
      var res2       = arrayDates[1].replace("Abr", "Apr");
      var date2      = new Date(res2);

      $('#date_start').val(date1.yyyymmmdd());
      $('#date_end').val(date2.yyyymmmdd());
    });
  });
</script>
@endsection