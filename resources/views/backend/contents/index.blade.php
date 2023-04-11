<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.admin-master')

@section('title') CONTENIDOS - FRONT @endsection

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
  
li.link a {
    background-color: #6d5cae;
    color: #FFF;
    font-weight: bold;
}
h4 {
    border-bottom: 1px solid #c3c3c3;
    margin-top: 1em;
}
  </style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12 text-center">
      <h2 class="font-w800">CONTENIDOS - FRONT</h2>
<!--      <div class="">
        <select id="select_site" class="form-control">
          <option value="-1">Granada</option>
        <?php foreach (\App\Sites::all() as $item): ?>
          <option value="<?php echo $item->id ?>" @if($item->id == $site) selected @endif><?php echo $item->name ?></option>
        <?php endforeach ?>
        </select>
      </div>-->
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
          <!--<a href="{{ url('admin/galleries') }}">Galerías</a>-->
      <h3>Listado de contenidos:</h3>
      <ul class="list-options">
      @foreach($lst as $k=>$v)
        <li><a href="{{route('contents.index',array($site,$k))}}">{{$v}}</a></li>
      @endforeach
      
      <li class="link"><a href="{{ url('admin/blogs') }}">Blogs</a></li>
      <li class="link"><a href="{{ url('admin/excursions') }}">Excursiones</a></li>
      </ul>
    
    </div>
    <div class="col-md-6">
      @if($current)
       <h3 class="font-w800">{{$lst[$current]}}</h3>
      <form method="POST" action="{{route('contents.upd',array($site,$current))}}" enctype="multipart/form-data">
        <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
         @foreach($fields as $k=>$v)
         <?php 
         if($v[1] == 'title'){
           echo '<h4>'.$v[0].'</h4>';
           continue;
         }
         if($v[1] == 'title_url'){
           echo '<h4><a href="'.$v[3].'" target="_back">'.$v[0].'</a></h4>';
           continue;
         }
          
         ?>
         <div class="form-material pt-1">
         <label>{{$v[0]}}</label>
         <?php 
         switch ($v[1]):
          case 'ckeditor':
            ?><textarea class="ckeditor" name="{{$k}}" id="{{$k}}" rows="10" cols="80">{{$v[2]}}</textarea><?php
            break;
          case 'file':
            ?>
            @if($v[2])
            <p>
            <img src="{{ $v[2] }}" style='max-width: 100%;'>
            </p>
            @endif
            <input name="{{$k}}" id="{{$k}}" type="file" class="custom-file-input" />
            <?php
            break;
          case 'string':
            ?>
            <input type="text" name="{{$k}}" id="{{$k}}" value="{{$v[2]}}" class="form-control">
            <?php
            break;
          case 'textarea':
            ?>
            <textarea type="text" name="{{$k}}" id="{{$k}}" rows="10" class="form-control">{{$v[2]}}</textarea>
            <?php
            break;
          case 'video':
            ?>
            @if($v[2])
            <p>
              <a href="{{ $v[2] }}" target="_black" title="Ver video">{{ $v[2] }}</a>
            </p>
            <p>
              <input type="checkbox" id="{{$k}}_remove" name="{{$k}}_remove"/> Eliminar
            </p>
            @endif
            <input name="{{$k}}" id="{{$k}}" type="file" class="custom-file-input" />
            <?php
            break;
         endswitch; 
         ?>
         </div>
        @endforeach
        <button class="btn btn-primary m-t-20">Guardar</button>
      </form>
      @endif
    </div>
  </div>
    
  </div>
  
  
</div>


@endsection

@section('scripts')

<script>
  $(document).ready(function () {
    $('#select_site').on('change', function(){
      location.href = '/admin/contents-home/'+$(this).val();
    });
  });

</script>
 <style>
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
 </style>
@endsection