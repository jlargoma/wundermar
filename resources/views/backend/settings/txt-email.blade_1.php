<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES"); 
?>
@extends('layouts.admin-master')

@section('title') Configuración TXT emails @endsection

@section('externalScripts')
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
<style>
  .list-options{
        margin: 0;
    padding: 0;
  }
  .list-options li{
    list-style: none;
    margin-bottom: 1em;
  }
  .list-options li a{
    padding: 7px;
    border: solid 1px #949494;
    width: 13em;
    box-shadow: 1px 1px 1px #000;
    margin-bottom: 7px;
    display: block;
  }
  </style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12 text-center">
      
      <div class="col-md-5 text-center">
        <h2 class="font-w800" style="margin: 0 0 1em;">CONFIGURACIONES - TEXTOS</h2>
      </div>
      <div class="col-md-4 text-center">
        <input id="select_site_lgn" type="hidden" value="{{$lng}}">
        <input id="select_site_key" type="hidden" value="{{$key}}">
        <select id="select_site" class="form-control">
        <?php foreach (\App\Sites::all() as $item): ?>
          <option value="<?php echo $item->id ?>" @if($item->id == $site) selected @endif><?php echo $item->name ?></option>
        <?php endforeach ?>
        </select>
         <?php if ($lng == 'es'): ?>
          <button class="btn btn-md btn-primary active mt-1em"  disabled>Español</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary mt-1em" href="{{$url_sp}}">Español</a>
<?php endif ?>	
          <?php if ($lng == 'en'): ?>
          <button class="btn btn-primary active mt-1em"  disabled>Ingles</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary mt-1em" href="{{$url_en}}">Ingles</a>
<?php endif ?>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-2">
        <ul class="list-options">
        @foreach($settings as $k=>$v)
        <?php  if( $k == 'reservation_state_changed_reserv_ota') { continue; } ?>
        <?php  if( $k == 'reservation_state_changed_reserv_ota_en') { continue; } ?>
         <li><a href="{{route('settings.msgs',array($site,$lng,$k))}}">{{$v}}</a></li>
        @endforeach
        </ul>
      </div>
      <div class="col-md-6">
          @foreach($settings as $k=>$v)
          <?php  
          if( $k == 'reservation_state_changed_reserv_ota') { continue; } 
          if( $k !== $key) { continue; } 
          ?>
          <div class="text-center">
            <h3 class="font-w800">{{$v}}</h3>
            <form method="POST" action="{{route('settings.msgs.upd',array($site,$lng))}}">
              <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="key" id="key" value="{{$k}}">
              <?php 
                $content = isset($data[$k]) ? trim($data[$k]) : ''; 
                $ckeditor = true;
                if ( $k =='SMS_Partee_msg' 
                    || $k == 'SMS_Partee_upload_dni'
                    || $k == 'SMS_Partee_upload_dni_en'
                    || $k == 'SMS_Partee_msg_en'
                    || $k == 'SMS_fianza'
                    || $k == 'SMS_fianza_en'
                    || $k == 'SMS_buzon'
                    || $k == 'SMS_buzon_en'
                    || $k == 'send_encuesta_subject'
                    || $k == 'send_encuesta_subject_en'
                  )
                  $ckeditor = false;
              ?>
              @if($ckeditor)
                @if( $k == 'reservation_state_changed_reserv' || $k == 'reservation_state_changed_reserv_ota'
                || $k == 'reservation_state_changed_reserv_en' || $k == 'reservation_state_changed_reserv_ota_en')
                  @if( $k == 'reservation_state_changed_reserv' ||  $k == 'reservation_state_changed_reserv_en')
                  <ul class="tabs-btn">
                    <li id="rvn_2">OTA</li>
                    <li class="active" id="rvn_1">Normal</li>
                  </ul>
                  <div class="tab-container">
                    <div id="rvn_1_content">
                      <textarea class="ckeditor" name="{{$k}}" id="{{$k}}" rows="20" cols="80">{{$content}}</textarea>
                    </div>
                    <div id="rvn_2_content">
                      <?php 
                      $k_ota = $k.'_ota';
                      if ($k == 'reservation_state_changed_reserv_en') $k_ota = 'reservation_state_changed_reserv_ota_en';
                      $content_2 = isset($data[$k_ota]) ? trim($data[$k_ota]) : '';  
                      ?>
                      <textarea class="ckeditor" name="{{$k_ota}}" id="{{$k_ota}}" rows="20" cols="80">{{$content_2}}</textarea>
                    </div>
                  </div>
                  @endif
                @else
                <textarea class="ckeditor" name="{{$k}}" id="{{$k}}" rows="20" cols="80">{{$content}}</textarea>
                @endif
              @else
                <textarea class="form-control" name="{{$k}}" id="{{$k}}" rows="20" cols="80">{{$content}}</textarea>
              @endif
              <button class="btn btn-primary m-t-20">Guardar</button>
            </form>
            <h2>Mensaje Whatsapp</h2>
            <?php 
            $whatsapp = whatsappFormat($content);
            $whatsapp = str_replace('%0D%0A', ' <br />', $whatsapp);
            $whatsapp = str_replace(' *', ' <b>', $whatsapp);
            $whatsapp = str_replace('* ', '</b> ', $whatsapp);
//              $text = convertBold($text);
              //comienzo de linea
              $whatsapp = str_replace('*', ' <b>', $whatsapp);
            ?>
            
            <iframe srcdoc="{{$whatsapp}}" style="width: 100%;min-height: 15em;border: 1px solid;"></iframe>
            <a href="/test-text/{{$lng}}/{{$k}}" title="ver pagina" target="_black">Ver Página >> </a><br>
            <a href="/test-text/{{$lng}}" title="ver Todos" target="_black">Ver Todos >> </a><br>
            
            <small>Nota: remover &AMP;nbsp; del código HTML (son los espacios en blanco)</small>
          </div>

          @endforeach
      </div>
      <div class="col-md-4">
          <div class="infomsg">
            <h2>Variables</h2>
                Nombre:<b>{customer_name}</b> <br>
                Teléfono:<b><a href="tel:{customer_phone}">{customer_phone}</a></b> <br>
                Apartamento: <b>{room}  {room_type}</b><br>
                Email:<b>{customer_email}</b> <br>
                Fecha Entrada:<b>{date_start}</b> <br>
                Fecha Salida:<b>{date_end}</b> <br>
                Noches:<b>{nigths}</b> <br>
                Ocupantes:<b>{pax}</b> <br>
                Suplemento lujo:<b>{sup_lujo} €</b> <br>
                Comentarios:<b>{comment}</b><br>
                Observaciones internas:<b>{book_comments}</b><br>
                Precio total:<b>{total_price} €</b><br>
                Tipo de habitación:<b>{room_type}</b><br>
                Nombre habitación:<b>{room_name}</b><br>
                Pago de la señal <b>{percent}% del total = {mount_percent} €</b><br>

                % restante de tu reserva (Recordatorio 2º pago):<b>{pend_percent}</b><br>
                Valor restante de tu reserva (Recordatorio 2º pago):<b>{pend_payment}</b><br>
                Url pago restante de tu reserva (Recordatorio 2º pago):<b>{urlPayment_rest}</b><br>
                Url pago :<b>{urlPayment}</b><br>
                Monto pago :<b>{payment_amount}</b><br>
                Cobrado (Recordatorio 2º pago):<b>{total_payment}</b><br>
                Partee link:<b>{partee}</b> <br>
                Orden Forfait:<b>{forfait_order}</b> <br>
                Link Forfait:<b>{link_forfait}</b> <br>
                Listado de cobros:<b>{cobros_list}</b> <br>
                Estado de cobro:<b>{cobros_estado}</b> <br>
                Precio Extras:<b>{total_adicionales}</b> <br>
                Precio OTA:<b>{priceOTA}</b> <br>
                Nombre Buzón/Caja:<b>{buzon}</b> <br>
                Clave Buzón/Caja:<b>{buzon_key}</b> <br>
                Color Buzón:<b>{buzon_color}</b> <br>
                Caja Buzón:<b>{buzon_caja}</b> <br>
                Google Encuesta:<b>{google_link}</b> <br>
          </div>
        </div>
    </div>
  
</div>


@endsection

@section('scripts')
 <script></script>
  <script type="text/javascript">
//        tinymce.init({selector:'textarea'});

      $(document).ready(function() {
        $('#rvn_2_content').hide();
        $('.tabs-btn').on('click','li',function(){
          var that = $(this);
          var id = that.attr('id');
          
          if (id == 'rvn_2'){
            $('#rvn_2_content').show();
            $('#rvn_1_content').hide();
            
          } else {
            $('#rvn_2_content').hide();
            $('#rvn_1_content').show();
          }
       
          $('.tabs-btn').find('li').removeClass('active');
          $('.tabs-btn').find('#'+id).addClass('active');
          
        });
        
        $('#select_site').on('change', function(){
          
          var url = '/admin/settings_msgs/'+$(this).val();
          var lgn = $('#select_site_lgn').val()
          if (lgn){
            url += '/'+lgn;
            var key = $('#select_site_key').val()
            if (key){
              url += '/'+key;
            }
          }
            
           
          location.href = url
        });
    
      });
      </script>
 <style>
   
   select#select_site {
    max-width: 190px;
    margin: 0em auto;
}

    .infomsg{
      font-size: 0.85em;
      background-color: #fff;
      padding: 3em;
      width: 80%;
      margin: 5em auto 0;
      box-shadow: 3px 1px 6px 1px;
      line-height: 1.85em;
    }
    
    .infomsg b{
      margin-left: 1em;
    }
    ul.tabs-btn{
      clear: both;
      overflow: auto;
    }
    ul.tabs-btn li {
    list-style: none;
    float: right;
    background-color: #6d5cae;
    color: #fff;
    padding: 3px 14px;
    margin: 0 1px;
    border-radius: 5px;
    cursor: pointer;
}
    ul.tabs-btn li.active {
          background-color: #a093c9;
    }
 </style>
@endsection
