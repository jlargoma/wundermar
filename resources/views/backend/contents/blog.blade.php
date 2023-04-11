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
      <h2>LISTADO DE <span class="font-w800">Blog</span></h2>
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
    <a type="button" class="btn btn-success btn-sm" href='{{route('contents.blog','new')}}'>
      <i class="fa fa-plus-circle" aria-hidden="true"></i>Agregar Nota
    </a> 
  </div>
    <div class="row">
    <div class="col-md-5 content-table-rooms">
      <table class="table table-condensed table-striped">
        <thead>
          <tr>
            <th class ="text-left bg-complete text-white font-s12" style="width: 40%;">Nota</th>
            <th class ="text-center bg-complete text-white font-s12" >Estado</th>
            <th class ="text-center bg-complete text-white font-s12" ></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($blogs as $item): ?>
            <tr>
              <td class="text-left" >{{$item->title}}</td>
              <td class="text-center" >
                                                    
              </td>
             
              <td class="text-center" >
                <a type="button" href="{{route('contents.blog',$item->id)}}" class="btn btn-success btn-sm editAptoText" title="Editar textos aptos">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a> 
                <a type="button" class="btn btn-default btn-sm" href="/blog/{{$item->name}}" target="_blank" data-original-title="Enlace de Apartamento" data-toggle="tooltip">
                  <i class="fa fa-paperclip"></i>
                </a>
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
              <form enctype="multipart/form-data" action="{{ route('contents.blog.edit') }}" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="blog" id="blog"  value="{{$obj->id ?? 'new'}}">
                <div class="form-group col-md-12">
                  <label for="Nombre">*Título</label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Título" required value="{{$obj->title ?? ''}}">
                </div>
                <div class="form-group col-md-12">
                  <label for="Nombre">*Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Slug Url" required value="{{$obj->name ?? ''}}">
                </div>
             
                <div class="form-group col-md-12">
                  <label for="Estado">Estado</label>
                  <select id="item_status" name="item_status" class="form-control">
                    <option value="1" @if($obj->status == 1) selected @endif>Publicado</option>
                    <option value="0" @if($obj->status == 0) selected @endif>No Publicado</option>
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
                  <label for="Nombre">SEO: Meta-title</label>
                  <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="SEO: Meta-title" required value="{{$obj->meta_title ?? ''}}">
                </div>
                <div class="form-group col-md-12">
                  <label for="Nombre">SEO: Meta-Descripción</label>
                  <textarea class="form-control" name="meta_descript" rows="6" id="meta_descript">{{$obj->meta_descript ?? ''}}</textarea>
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

@endsection

@section('scripts')

<script src="/assets/js/notifications.js" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready(function () {
  
});
</script>
@endsection