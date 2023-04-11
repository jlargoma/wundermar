<?php

use \App\Classes\Mobile;

$mobile = new Mobile();
?>
@extends('layouts.admin-master')

@section('title') Administrador de Items Forfaits @endsection

@section('externalScripts') 
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 table-responsive">
  
  <div class="row">
    <div class="col-md-12 text-center">
      <h2>LISTADO DE <span class="font-w800"> ITEMS</span></h2>
    </div>
    <div class="col-md-12 text-center">
      <div class="btn-contabilidad">
        <?php if (Request::path() == 'admin/forfaits/orders'): ?>
          <button class="btn btn-md text-white active"  disabled>Control FF</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary" href="{{url('/admin/forfaits/orders')}}">Control FF</a>
        <?php endif ?>	
      </div>

      <div class="btn-contabilidad">
        <?php if (Request::path() == 'admin/forfaits'): ?>
          <button class="btn btn-md text-white active"  disabled>Items FF</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary" href="{{url('/admin/forfaits')}}">Items FF</a>
        <?php endif ?>	
      </div>
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
    <div class="col-md-5 col-xs-12 content-table-rooms">
      <table class="table ">
        <thead>
          <tr>
            <th class ="text-center bg-complete text-white" id="select-filter" >
              <select id="change_categ" name="change_categ" class="form-control">
                <option value="">Categoría</option>
                @foreach ($categ as $catID => $catName):
                <option value="{{$catID}}" <?php if ($selClass == $catID) echo 'selected'; ?>>{{$catName}}</option>
                @endforeach
              </select>
            </th>
            <th class ="text-center bg-complete text-white font-s12" style="width: 30%;">NOMBRE</th>
            <th class ="text-center bg-complete text-white font-s12" >TIPO</th>
            <th class ="text-center bg-complete text-white font-s12" >ESTADO</th>
            <th class ="text-center bg-complete text-white font-s12" >&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($categ as $catID => $catName):
            if (isset($items[$catID])):
              foreach ($items[$catID] as $item):
                ?>
                <tr>
                  <td class="text-left" style="width: 150px;" >{{$catName}}</td>
                  <td class ="text-center">{{$item->name}}</td>
                  <td class="text-center" >{{$item->type}}</td>
                  <td class="text-center" ><?php echo ($item->status) ? 'ACTIVO' : 'NO ACTIVO'; ?></td>
                  <td class="text-center" >
                    <button type="button" class="btn btn-success updateItem" data-id="{{$item->id}}" title="Editar">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>                    
                  </td>
                </tr>
                <?php
              endforeach;
            endif;
          endforeach;
          ?>

        </tbody>
      </table>
    </div>
    <div class="col-md-5 col-xs-12 content-table-rooms">
      <form method="POST" action="/admin/forfaits/upd">
        <input type="hidden" id="item_id" name="item_id" value="">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="KEY">KEY</label>
            <input type="text" class="form-control" id="item_key" name="item_key" readonly="">
          </div>
          <div class="form-group col-md-4">
            <label for="Categoría">Categoría</label>
            <input type="text" class="form-control" id="item_cat" readonly="">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="Nombre">*Nombre</label>
            <input type="text" class="form-control" id="item_nombre" name="item_nombre" placeholder="Introduce aqui tu Nombre" maxlength="40" required>
          </div>
          <div class="form-group col-md-4">
            <label for="Tipo">Tipo</label>
            <input type="text" class="form-control" id="item_tipo" name="item_tipo" placeholder="" maxlength="40">
          </div>
          <div class="form-group col-md-4">
            <label for="Estado">Estado</label>
            <select id="item_status" name="item_status" class="form-control">
              <option value="1">Publicado</option>
              <option value="0">No Publicado</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="regular_price">*Precio Regular</label>
            <input type="text" class="form-control" id="regular_price" name="regular_price" placeholder="" maxlength="40" required>
          </div>
          <div class="form-group col-md-6">
            <label for="special_price">*Precio Fin de semana</label>
            <input type="text" class="form-control" id="special_price" name="special_price" placeholder="" maxlength="40" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-12">
            <label for="Nombre">Descripción</label>
            <textarea class="" name="item_equip" id="item_equip" rows="10" cols="80"></textarea>
          </div>
        </div>  
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="hour_start">Hora de Inicio</label>
            <input type="text" class="form-control" id="hour_start" name="hour_start" placeholder="" maxlength="3">
          </div>
          <div class="form-group col-md-6">
            <label for="hour_end">Hora de cierre</label>
            <input type="text" class="form-control" id="hour_end" name="hour_end" placeholder="" maxlength="3">
          </div>
        </div>
        <div class="form-row btn-save-forfait">
          <button class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
  .btn-save-forfait{ 
    width: 100%;
    text-align: center;
  }
  .form-row{
    display: block;
    overflow: auto;
  }
  #select-filter{
    padding: 0 !important;
    margin: 0;
    width: 130px;
  }
  select#change_categ {
    width: 120px;
    padding: 4px;
    margin-left: 4px;
  }
</style>
<script src="/assets/js/notifications.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function () {
  CKEDITOR.replace('item_equip',
          {
            toolbar:
                    [
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
                '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            '/',
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
                    ]
          });

  $('#change_categ').on('change', function (event) {
    location.replace('/admin/forfaits/' + $(this).val());
  });
  $('.updateItem').click(function (event) {
    var id = $(this).data('id');
    $.get('/admin/forfaits/edit/' + id, function (data) {

//        $('#item_class').val(data.item.class);
//        $('#item_equip').val(data.item.equip);
      $('#item_key').val(data.item.item_key);
      $('#item_id').val(data.item.id);
      $('#item_nombre').val(data.item.name);
      $('#item_tipo').val(data.item.type);
      $('#item_status').val(data.item.status);
      $('#regular_price').val(data.item.regular_price);
      $('#special_price').val(data.item.special_price);
      $('#item_cat').val(data.cat);
      $('#hour_start').val(data.item.hour_start);
      $('#hour_end').val(data.item.hour_end);
      
      CKEDITOR.instances.item_equip.setData(data.item.equip, function () {
        this.checkDirty();
      });
      CKEDITOR.instances.item_class.setData(data.item.class, function () {
        this.checkDirty();
      });

    });
  });

});
</script>
@endsection