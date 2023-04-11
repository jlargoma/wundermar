<?php
use \Carbon\Carbon;
use App\Classes\Mobile;
$mobile = new Mobile();
$uRole = getUsrRole();
?>
@extends('layouts.admin-master')
@section('title') Administrador de reservas @endsection

@section('externalScripts')
@include('backend.planning.blocks.styles-update')
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid padding-10 sm-padding-10" id="updateBooking">
 @include('backend.planning.blocks.header-update')
    <div class="row center text-center">
      <!-- DATOS DE LA RESERVA -->
      <div class="col-md-6 col-xs-12">
        <div class="overlay loading-div" style="background-color: rgba(255,255,255,0.6); ">
          <div style="position: absolute; top: 50%; left: 35%; width: 40%; z-index: 1011; color: #000;">
            <i class="fa fa-spinner fa-spin fa-5x"></i><br>
            <h3 class="text-center font-w800" style="letter-spacing: -2px;">CALCULANDO...</h3>
          </div>
        </div>
        <form role="form" id="updateForm"  action="{{ url('/admin/reservas/saveUpdate') }}/<?php echo $book->id ?>" method="post">
          <textarea id="computed-data" style="display: none"></textarea>
          <input id="bkgID" type="hidden" name="book_id" value="{{ $book->id }}">
          <input id="update_php" type="hidden" name="update_php" value="1">
          <!-- DATOS DEL CLIENTE -->
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $book->customer->id ?>">
          @include('backend.planning.blocks.form-upd-A')
          @include('backend.planning.blocks.form-upd-B')
          @include('backend.planning.blocks.form-upd-C')
          <div class="col-xs-12 mb-1em mt-1em">
           @include('backend.planning.blocks._show_extra')
          </div>
          <div class="col-xs-12 bg-white padding-block">
            <div class="col-sm-4 col-xs-12 mb-1em">
              <label>Comentarios Cliente </label>
              <textarea class="form-control" name="comments" rows="5"
                        data-idBook="<?php echo $book->id ?>"
                        data-type="1"><?php echo $book->comment ?></textarea>
            </div>
            <div class="col-sm-4 col-xs-12 mb-1em">
              <label>Comentarios Internos</label>
              <textarea class="form-control book_comments" name="book_comments" rows="5"
                        data-idBook="<?php echo $book->id ?>"
                        data-type="2"><?php echo $book->book_comments ?></textarea>
            </div>
            <div class="col-sm-4 col-xs-12 content_book_owned_comments mb-1em">
              <label>Comentarios Propietario</label>
              <textarea class="form-control book_owned_comments" name="book_owned_comments" rows="5"
                        data-idBook="<?php echo $book->id ?>"
                        data-type="3"><?php if (!empty($book->book_owned_comments)): ?><?php echo $book->book_owned_comments ?><?php endif; ?></textarea>
            </div>
          </div>
          <div class="col-xs-12">
              <button class="btn btn-success font-s24 font-w400 mb-1em mt-1em" type="submit" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>Guardar
              </button>
          </div>
        </form>
      </div>
      <div class="col-md-6 col-xs-12 padding-block">
      @include('backend.planning.blocks.form-upd-rigth')
    </div>

  </div>
  <button style="display: none;" id="btnEmailing" class="btn btn-success btn-cons m-b-10" type="button"
          data-toggle="modal" data-target="#modalEmailing"></button>


  <form role="form">
    <div class="form-group form-group-default required" style="display: none">
      <label class="highlight">Message</label>
      <input type="text" hidden="" class="form-control notification-message"
             placeholder="Type your message here" value="This notification looks so perfect!" required>
    </div>
    <button class="btn btn-success show-notification hidden" id="boton">Show</button>
  </form>
  <input type="hidden" class="precio-oculto" value="<?php echo $book->total_price ?>">
 @include('backend.planning.blocks.modals-update')
</div>
@endsection