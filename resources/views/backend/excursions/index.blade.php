<?php

use \App\Classes\Mobile;

$mobile = new Mobile();
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
  .name-back{
    background-color: rgba(72,176,247,0.5)!important;
  }
  .name-back input{
    background-color: transparent;
    color: black;
    font-weight: 800;
  }
  .ocupation-back{
    background-color: rgba(72,176,247,0.5)!important;
  }
  .ocupation-back input{
    background-color: transparent;
    color: black;
    font-weight: 800;
  }
  
  #tit_apto{
    text-align: center;
    font-size: 1.3em;
    width: 94%;
    background-color: #337cae;
    margin: 8px 3%;
    color: #fff;
  }
</style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 table-responsive">
  <div class="row">
    <div class="col-md-12 text-center">
      <h2>LISTADO DE <span class="font-w800">Excursiones</span></h2>
    </div>
    
  </div>
    @if($errors->any())
      <p class="alert alert-danger">{{$errors->first()}}</p>
    @endif
    @if (\Session::has('success'))
    <p class="alert alert-success">{!! \Session::get('success') !!}</p>
    @endif
  <div class="clearfix"></div>
  <div class="row">
    <a type="button" class="btn btn-success btn-sm" href='{{route('excursions','new')}}'>
      <i class="fa fa-plus-circle" aria-hidden="true"></i>Agregar
    </a> 
  </div>
    <div class="row">
    <div class="col-md-5 content-table-rooms">
      <table class="table table-condensed table-striped">
        <thead>
          <tr>
            <th class ="text-left bg-complete text-white font-s12" style="width: 40%;">Nombre</th>
            <th class ="text-center bg-complete text-white font-s12" >Estado</th>
            <th class ="text-center bg-complete text-white font-s12" >Galería</th>
            <th class ="text-center bg-complete text-white font-s12" ></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lstItems as $item): ?>
            <tr>
              <td class="text-left" >{{$item->title}}</td>
              <td class="text-center" >{{$item->getStatus()}}</td>
              <td class="text-center" >
                <button type="button" class="btn btn-success btn-sm uploadFile" data-toggle="modal" data-target="#modalFiles" data-id="{{$item->id}}" title="Subir imagenes aptos">
                  <i class="fa fa-upload" aria-hidden="true"></i>
                </button>                    
              </td>
              <td class="text-center" >
                <a type="button" href="{{route('excursions',$item->id)}}" class="btn btn-success btn-sm editAptoText" title="Editar textos aptos">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a> 
                <form action="{{ route('excursions.delete') }}" method="POST" style="display:inline-block">
                  {{csrf_field()}}
                  <input type="hidden" id="del" name="del" value="{{$item->id}}">
                  <button class="btn btn-danger btn_remove" title="Eliminar excursión" onclick="return confirm('¿Quieres Eliminar la excursión?');">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
      <div class="col-md-7">
      @if($obj ||$new)
        <div class="block">
          <div class="container-xs-height full-height" id="content_apto">
              <form enctype="multipart/form-data" action="{{ route('excursions.edit') }}" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="id" id="id"  value="{{$obj->id ?? 'new'}}">
                <div class="form-group col-md-12">
                  <label for="Nombre">*Título</label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Título" required value="{{$obj->title ?? ''}}">
                </div>
                <div class="form-group col-md-12">
                  <label for="Nombre">*Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Slug Url" required value="{{$obj->name ?? ''}}">
                </div>
                <div class="form-group col-md-6">
                  <label for="Estado">Estado</label>
                  <select id="item_status" name="item_status" class="form-control">
                    <option value="1" @if($obj->status == 1) selected @endif>Publicado</option>
                    <option value="2" @if($obj->status == 2) selected @endif>Destacado</option>
                    <option value="0" @if($obj->status == 0) selected @endif>No Publicado</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label for="tag">Etiqueta</label>
                  <select id="tag" name="tag" class="form-control">
                    <option value="">--</option>
                    @foreach($tags as $k=>$v)
                    <option value="{{$k}}" @if($obj->tag == $k) selected @endif>{{$v}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label>*Precio Final</label>
                  <input type="text" class="form-control" id="price" name="price" placeholder="Precio Final" required value="{{$obj->price ?? ''}}">
                </div>
                <div class="form-group col-md-4">
                  <label>*Precio Base</label>
                  <input type="text" class="form-control" id="price_basic" name="price_basic" placeholder="Precio Base" required value="{{$obj->price_basic ?? ''}}">
                </div>
                <div class="form-group col-md-4">
                  <label for="starts">*Puntuación</label>
                  <select id="starts" name="starts" class="form-control">
                    <option value="">--</option>
                    @for($i=0;$i<6;$i++)
                    <option value="{{$i}}" @if($obj->starts == $i) selected @endif>{{$i}}</option>
                    @endfor
                  </select>
                </div>
                <div class="form-group col-md-12">
                  <label for="Nombre">Descripción</label>
                  <textarea class="ckeditor" name="content" id="content" rows="5" cols="80">{{$obj->content ?? ''}}</textarea>
                </div>
                
                <div class="form-group col-md-12">
                    <label>Imagen principal</label>
                    @if (trim($obj->img) != '')
                    <p>
                    <img src="{{ $obj->img }}" style='max-width: 100%;'>
                    </p>
                    @endif
                    <input name="imagen" id="imagen" type="file" class="custom-file-input">
                </div>
                
                <div class="form-group col-md-12">
                  <label for="video">Video</label>
                  <input type="text" class="form-control" id="video" name="video" placeholder="Url video" value="{{$obj->video ?? ''}}">
                </div>

                <div class="form-group col-md-12">
                  <input type="submit" value="Enviar" class="btn btn-primary" />
                </div>
              </form>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>



<div class="modal fade slide-up in" id="modalFiles" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xs">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center">
              Subida de archivos
            </h2>
            <div class="text-center">
              <small class="text-danger">Recomendado 1024*680 px</small>
            </div>
            
          </div>
          <div class="container-xs-height full-height">
            <div class="row-xs-height">
              <div class="modal-body col-xs-height col-middle text-center   ">
                <div class="upload-body">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

<script src="/assets/js/notifications.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function () {
  $('body').on('click','.uploadFile',function(event) {
    var id = $(this).attr('data-id');
    $.get('{{route("excursions.gallery")}}/'+id, function(data) {
      $('#modalFiles').find('.upload-body').empty().append(data);
    });
  });
  
});
</script>
@endsection