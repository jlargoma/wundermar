@extends('layouts.admin-master')

@section('title') Promociones @endsection

@section('externalScripts') 
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
      integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<style>

  #datebox input.daterange01 {
    height: 3em;
    margin: 7px 5px;
    padding: 1em 2px;
    text-align: center;
  }
  .box-row{
    padding: 1em 2em !important;
  }
  .box{ background-color: #FFF;}
  .box ul {padding: 5px;}
  .box ul li {list-style: none;}
  select[multiple], select[size] {
    height: 18em !important;
  }
  form label {font-size: 11px;}
  .mt-2em {margin-top: 2em;}
  button.btn.btn_weekday {padding: 6px;}
  button.btn.btn_weekday.active {
    background-color: #2b5d9b;
    color: #FFF;
  }
  .blockType {
    margin: 2em 0;
    max-width: 48%;
    margin-left: 2%;
  }
  .row.promo_type input {background-color: #000 !important;}
  .row.promo_type.checked input {background-color: #FFF !important;}
  a.selAll {
    color: #3a8fc8;
    font-size: 10px;
    text-transform: lowercase;
  }
  i.rm_exept.fa.fa-trash {
    color: red;
    cursor: pointer;
  }
</style>

<div class="container-fluid padding-25 sm-padding-10">
  @if (\Session::has('sent'))<p class="alert alert-success">{!! \Session::get('sent') !!}</p>@endif
  @if($errors->any())<p class="alert alert-danger">{{$errors->first()}}</p>@endif
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3 col-xs-12">
          <h3>Promociones de Temporadas:</h3>
        </div>
        <div class="col-xs-12 col-md-7">
          @include('backend.prices._navs')
        </div>
        <div class="col-md-2 col-xs-12 mt-2em">
          @include('backend.years._selector', ['minimal' => true])
        </div>
        @if (Auth::user()->email == "jlargo@mksport.es")
          <div class="col-md-12 col-xs-12">
            <form action="{{route('precios.prepare-cron')}}" method="post" class="inline">
              <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
              <button class="btn btn-success" title="{{$sendDataInfo}}">Sincr. precios OTAs</button>
            </form>
            <small>(Sincronizar toda la temporada)</small>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="row">
    <div class="pt-1 col-md-6 col-lg-5 col-xs-12">
      @include('backend.prices.blocks._promo_form')
    </div>
    <div class="col-md-6 col-lg-7 col-xs-12 box-row">
      @if($lstPromotions)
      @foreach($lstPromotions as $item)
      <div class="box row box_promotion">
        <div class="col-md-4">
          <h4>{{$item['name']}}</h4>
          <h5>{{$item['value']}}</h5>
          <strong>{{$item['start'].' - '.$item['finish']}}</strong>
          <br>
          @if($item['weekDay'])
          <p class="alert alert-warning">{{$item['weekDay']}}</p>
          @endif
          <br>
          <button class="btn btn-default editPromotion" type="button" data-id="{{$item['id']}}">Editar</button>
          <button class="btn btn-danger deletePromotion" type="button" data-id="{{$item['id']}}">Eliminar</button>

        </div>
        <div class="col-md-4">
          <strong>Apartamentos</strong><br><br>
          <?php if($item['rooms']){ echo implode(', ',$item['rooms']); }?>
        </div>
        <div class="col-md-4">
          <strong>Excepciones</strong>
          @if($item['except'])
          <ul>
            @foreach($item['except'] as $day)
            <li>{{$day}}</li>
            @endforeach
          </ul>
          @endif
        </div>
      </div>
      @endforeach
      @endif
    </div>

  </div>
</div>
<div class="row"></div>
</div>


@endsection

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<script type="text/javascript" src="/js/datePicker01.js"></script>
<script type="text/javascript">
$(document).ready(function () {
  /********************************************************/
  var date_excl = 0;
  $('.datepicker').datepicker();
  $('#add').click(function () {
    date_excl++;
    var name = 'date_' + date_excl;
    // APPEND NEW DATE AND BIND IT AT THE SAME TIME
    var content = $('<div/>', {class: 'date_excl'});
    var datepickerConf = window.dateRangeObj;
    var input = $('<input/>', {name: name, type: 'text', class: 'daterange01'}).daterangepicker(datepickerConf);
    var inputBtn = $('<i/>', {tipe: "button", type: 'button', class: 'rm_exept fa fa-trash'}).data('id',name);
    content.append(input).append(inputBtn);
    $('#datebox').append(content);
  });
    
  $('#datebox').on('click','.rm_exept',function () {
    $("#datebox").find('[name="'+$(this).data('id')+'"]').remove();
    $(this).remove();
  });
  /********************************************************/
  $('.btn_weekday').on('click',function () {
        $(".btn_weekday").removeClass('active');
        $(this).addClass('active');
        $('#weekday').val($(this).data('val'));
  });
  $('.radioType').on('change',function () {
        $(".promo_type").removeClass('checked');
        $(this).closest('.promo_type').addClass('checked');
  });
  $('.selAll').on('click',function (e) {
    e.preventDefault();
    $(".aptos_check").prop('checked', true);
  });
  /********************************************************/
  $('#new').on('click',function () {
        $("#discount").val(15);
        $("#itemID").val(null);
        $('#datebox').html('');
        $('.form-check-input').prop('checked', true);
  });
  $('.editPromotion').on('click',function () {
    var id = $(this).data('id');
    var url = "{{route('channel.promotions.get')}}/"+id;
    $.get(url, function(resp) {
      if (resp == 'not_found'){
      } else {
        $("#ch_group").val('');
        $("#discount").val(resp.value);
        $("#itemID").val(id);
        $("#datepicker").val(id);
        $('#range').data('daterangepicker').setStartDate(resp.start);
        $('#range').data('daterangepicker').setEndDate(resp.finish);
        
        
        $("#name").val(resp.name);
        $("#nights").val(resp.nights);
        $("#night_apply").val(resp.night_apply);
        $("#weekday").val(resp.weekday);
        
        $(".btn_weekday").removeClass('active');
        $('.weekday_'+resp.weekday).addClass('active');
        $('#weekday').val(resp.weekday);
        
        let checkType = "#type_"+resp.type;
        $(checkType).attr('checked',true);
        $(".promo_type").removeClass('checked');
        $(checkType).closest('.promo_type').addClass('checked');
        
        
        
        

    
        $('#datebox').html('');
        let dateRangeObj = Object.assign({}, window.dateRangeObj);
        for(var i in resp.except){
          var name = 'date_' + i;
          dateRangeObj.startDate = resp.except[i].start;
          dateRangeObj.endDate = resp.except[i].end;
          var content = $('<div/>', {class: 'date_excl'});
          var input = $('<input/>', {name: name, id: name, type: 'text', class: 'daterange01'}).daterangepicker(dateRangeObj);
          var inputBtn = $('<i/>', {tipe: "button", type: 'button', class: 'rm_exept fa fa-trash'}).data('id',name);
          
          content.append(input).append(inputBtn);
          $('#datebox').append(content);
          date_excl = i;
        }
        
        for(var i in resp.rooms){
          $("#apto"+resp.rooms[i]).prop('checked', true);
        }
      }
      });
    
  });
  
  
  $('.deletePromotion').click(function (event) {
          
          if (confirm('Eliminar la promoción?')){
            var data = {
              id: $(this).data('id'),
              _token: "{{csrf_token()}}"
            };
            
            var elemet = $(this).closest('.box_promotion');
            
            $.ajax({
                url: "{{route('channel.promotions.delete')}}",
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
  
});
</script>
@endsection