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
    display: inline-block;
  }
  .list-options li a{
    padding: 7px;
    border: solid 1px #949494;
    box-shadow: 1px 1px 1px #000;
    margin: 2px;
  }
</style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10">
  <div class="col-md-5 text-center">
    <h2 class="font-w800" style="margin: 0 0 1em;">CONFIGURACIONES - TEXTOS</h2>
  </div>
  <div class="col-md-4">
  </div>
  <div class="col-md-3">
    <button type="button" data-toggle="modal" data-target="#modal_variables"><i class="fa fa-eye"></i> Variables</button>
  </div>
  <div class="col-md-12">
    <ul class="list-options">
      @foreach($settings as $k=>$v)
      <li><a href="{{route('settings.msgs',array($site,$k))}}" <?php if ($k == $key) echo 'class="active"'; ?>>{{$v}}</a></li>
      @endforeach
    </ul>
  </div>
  <div class="col-md-12 text-center  mt-1em">
    <ul class="list-options">
      @foreach(\App\Sites::allSites() as $k=>$v)
      <li><a href="{{route('settings.msgs',array($k,$key))}}" <?php if ($k == $site) echo 'class="active"'; ?>>{{$v}}</a></li>
      @endforeach
    </ul>
  </div>
  <div class="col-md-12 text-center  mt-1em">
    <a href="/test-text/{{$lng}}/{{$key}}" title="ver pagina" target="_black">Ver Página >> </a><br>
  </div>
  <div class="row">
    <div class="col-md-12 text-center">
      <div class="col-md-5 text-center">
      </div>
    </div>
    <div class="row ">
      <div class="col-md-12 text-center">
        <h2 class="font-w800">{{$settings[$key]}}</h2>
          @if( $key == 'reservation_state_changed_reserv')
          <ul class="tabs-btn">
            <li class="active" data-id="rvn_1">Normal</li>
            <li data-id="rvn_2">OTA</li>
          </ul>
           @endif
      </div>
      <div class="col-md-12" style="height: 3px;background-color: #6e5cae;margin: 1em 0px;"></div>
      <form method="POST" action="{{route('settings.msgs.upd',array($site,'es'))}}">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="key" id="key" value="{{$key}}">
      <div class="col-md-6 fc-1 fbox">
        <div class="text-center">
          <h3 class="subtit">Español</h3>
            @if($ckeditor)
            @if( $key == 'reservation_state_changed_reserv')
            <div class="tab-container">
              <div class="rvn_1_content">
                <textarea class="ckeditor" name="{{$key}}" id="{{$key}}" rows="20" cols="80">{{$data['es']}}</textarea>
              </div>
              <div class="rvn_2_content">
                <textarea class="form-control" name="{{$key.'_ota'}}" id="{{$key.'_ota'}}" rows="20" cols="80">{{strip_tags($data['es_ota'])}}</textarea>
              </div>
            </div>
            @else
            <textarea class="ckeditor" name="{{$key}}" id="{{$key}}" rows="20" cols="80">{{$data['es']}}</textarea>
            @endif
            @else
            <textarea class="form-control" name="{{$key}}" id="{{$key}}" rows="20" cols="80">{{$data['es']}}</textarea>
            @endif
           
        </div>
      </div>
      <div class="col-md-6 fc-2 fbox">
        <div class="text-center">
          <h3 class="subtit">Ingles</h3>
          <?php $k_en = $key.'_en'; ?>
            @if($ckeditor)
            @if( $key == 'reservation_state_changed_reserv')
            <div class="tab-container">
              <div class="rvn_1_content">
                <textarea class="ckeditor" name="{{$k_en}}" id="{{$k_en}}" rows="20" cols="80">{{$data['en']}}</textarea>
              </div>
              <div class="rvn_2_content">
                <textarea class="form-control" name="{{$key.'_ota_en'}}" id="{{$key.'_ota_en'}}" rows="20" cols="80">{{strip_tags($data['en_ota'])}}</textarea>
              </div>
            </div>
            @else
            <textarea class="ckeditor" name="{{$k_en}}" id="{{$k_en}}" rows="20" cols="80">{{$data['en']}}</textarea>
            @endif
            @else
            <textarea class="form-control" name="{{$k_en}}" id="{{$k_en}}" rows="20" cols="80">{{$data['en']}}</textarea>
            @endif
        </div>
      </div>
      <div class="col-xs-12 text-center">
        <button class="btn btn-primary m-t-20">Guardar</button>
      
      </div>
      <div class="col-xs-12">
        @if(in_array($key,$kWSP))
        <br/>
        <strong>Negrita:</strong> Para escribir texto en <b>negrita</b>, coloca un asterisco antes y después del texto:
        <br/>*texto*  (si es el final de una linea, agregar un espacio luego)
        @endif
      </div>
    </form>
    </div>
  </div>
  </div>


  <div class="modal fade slide-up in" id="modal_variables" tabindex="-1" role="dialog" aria-hidden="true" style=" z-index: 9999;">
    <div class="modal-dialog modal-xd">
      <div class="modal-content-classic">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
          <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
        </button>
        <h3>Variables</h3>
        @foreach($varsTxt as $k=>$v)
        <b>{{$v}}:</b> {{$k}}<br/>
        @endforeach
      </div>
    </div>
  </div>

  @endsection

  @section('scripts')
  <script></script>
  <script type="text/javascript">
//        tinymce.init({selector:'textarea'});

$(document).ready(function () {
  $('.rvn_2_content').hide();
  $('.tabs-btn').on('click', 'li', function () {
    var that = $(this);
    var id = that.data('id');
    if (id == 'rvn_2') {
      $('.rvn_2_content').show();
      $('.rvn_1_content').hide();

    } else {
      $('.rvn_2_content').hide();
      $('.rvn_1_content').show();
    }

    $('.tabs-btn').find('li').removeClass('active');
    that.addClass('active');

  });

  $('#select_site').on('change', function () {

    var url = '/admin/settings_msgs/' + $(this).val();
    var lgn = $('#select_site_lgn').val()
    if (lgn) {
      url += '/' + lgn;
      var key = $('#select_site_key').val()
      if (key) {
        url += '/' + key;
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
a.active {
    background-color: #004a2f;
    color: #FFF;
    font-weight: bold;
}
.col-md-6.fc-1 {padding-right: 2em !important;}
.col-md-6.fc-2 {padding-left: 2em !important;}
h3.subtit{
      line-height: 0.6;
      text-align: left;
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
      margin-top: -1em;
      padding: 0;
    }
    ul.tabs-btn li {
      list-style: none;
      display: inline-block;
      border:1px solid #004a2f;
      color: #004a2f;
      padding: 3px 14px;
      margin: 0 1px;
      border-radius: 5px;
      cursor: pointer;
          width: 90px;
    }
    ul.tabs-btn li.active {
      background-color: #004a2f;
      color: #fff;
    }
  </style>
  @endsection
