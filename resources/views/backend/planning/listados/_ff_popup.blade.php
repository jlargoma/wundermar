<?php   use \Carbon\Carbon;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$ff_status = [
  'new' => 'Nuevo',  
  'not_payment' => 'No pagado',  
  'payment' => 'Pagado',  
];
?>
@extends('layouts.popup')

@section('title') Administrador de reservas MiramarSKI @endsection

@section('externalScripts')
<style>
  .update_ff_status{
background-color: #eaeaea;
    border: solid 2px #d2d2d2;
    padding: 7px 10px;
    margin: 3px;
    color: inherit;
    border-radius: 3px;
  }
  .update_ff_status.active{
    color:#ffffff; 
    background-color:green;
  }
  .bold{
        margin-top: 11px;
    font-weight: 600;
  }
.table-responsive {
    margin: 1em auto;
    box-shadow: 1px 1px 8px #bdbdbd;
}
h1{
  background-color: #295d9b;
    color: #dfe6ef;
    padding: 7px;
    box-shadow: 1px 1px 3px #295d9b;
    margin-bottom: 0.7em;
    margin-top: 7px;
}
h2 {
    font-size: 1.2em;
    width: 100%;
    border-bottom: 1px solid #c1c1c1;
    padding-top: 1em;
}
.table{
  margin-bottom: 0;
}
thead,tfoot {
  background-color: #f5f5f5;
}
form{
  margin: 2em 0;
  border-radius: 3px;
  padding: 1em;
  box-shadow: 1px 1px 8px #bdbdbd;
}
.item_name{
  position: relative;
}
.item_detail{
  display: none;
    left: 75px;
    width: 16em;
    position: absolute;
    background-color: #ffffff;
    padding: 8px;
    border: 2px solid #c3c3c3;
    box-shadow: 1px 1px 8px #ababab;
    top: 37px;
    z-index: 164;
}
.table-responsive{
      overflow-x: initial;
}
.cart-container {
    margin-bottom: 7em;
}
  </style>
@endsection

@section('content')

  @if($errors->any())
  <p class="alert alert-danger">{{$errors->first()}}</p>
  @endif
  @if (\Session::has('success'))
  <p class="alert alert-success">{!! \Session::get('success') !!}</p>
  @endif
  
<h1>Detalles de la Reserva</h1>

<div class="row">
    <div class="col-md-2 bold">Estado</div>
    <a class="update_ff_status @if($book->ff_status == 4) active @endif" href="/admin/reservas/ff_change_status_popup/{{$book->id}}/4" >Comprometida</a>
    <a class="update_ff_status @if($book->ff_status == 3) active @endif" href="/admin/reservas/ff_change_status_popup/{{$book->id}}/3" >Confirmada</a>
    <a class="update_ff_status @if($book->ff_status == 2) active @endif" href="/admin/reservas/ff_change_status_popup/{{$book->id}}/2" >No Cobrada</a>
    <a class="update_ff_status @if($book->ff_status == 1) active @endif" href="/admin/reservas/ff_change_status_popup/{{$book->id}}/1" >Cancelada</a>
    <a class="update_ff_status @if($book->ff_status == 0) active @endif" href="/admin/reservas/ff_change_status_popup/{{$book->id}}/0" >No Gestionada</a>
</div>

<div class="table-responsive">
  <table class="table table-striped">
  <tbody>
    <tr>
      <th scope="row" >Número de la Reserva</th>
      <td>{{$book->id}}</td>
    </tr>
    <tr>
      <th scope="row" >Fecha de Solicitud de Reserva</th>
      <td>{{date('d/m/Y H:i',strtotime($book->created_at))}}</td>
    </tr>
    <tr>
      <th scope="row" >Nombre del Cliente</th>
      <td>{{$customer->name}}</td>
    </tr>
    <tr>
      <th scope="row" >Teléfono</th>
      <td>{{$customer->phone}}</td>
    </tr>
    <tr>
      <th scope="row" >Total</th>
      <td><?php if (isset($ff_data['total'])) echo str_replace('.',',',$ff_data['total']); ?>€</td>
    </tr>
    <tr>
      <th scope="row" >Estado Forfait</th>
      <td>
        <?php 
        if (isset($ff_data['status']) && isset($ff_status[$ff_data['status']])):
          echo $ff_status[$ff_data['status']];
        endif;
        if (isset($ff_data['ffexpr_status']) && $ff_data['ffexpr_status'] == 1):
          echo ' - ForfaitExpress reservado';
        else:
          echo ' - ForfaitExpress no reservado';
        endif;
          
        ?>
      </td>
    </tr>
    <tr>
      <th scope="row" >Fecha de Solicitud Forfait</th>
      <td>
        <?php 
        if (isset($ff_data['created'])):
          echo date('d/m/Y H:i',strtotime($ff_data['created']));
        endif;
        ?>
      </td>
    </tr>
    <tr>
    <tr>
      <th scope="row" >Booking Number (ForfaitExpress)</th>
      <td>
        {{$ff_data['bookingNumber']}}
      </td>
    </tr>
    <tr>
      <th scope="row" >Dirección de recogida</th>
      <td>
        {{$pickupPointAddress}}
      </td>
    </tr>
    <tr>
      <th scope="row" >Método de Pago</th>
      <td></td>
    </tr>
    <tr>
      <th scope="row" >Anotaciones del Cliente </th>
      <td>{{$customer->comments}}</td>
    </tr>
    <tr>
      <th scope="row" >Anotaciones de la Reserva</th>
      <td>{{$book->book_comments}}</td>
    </tr>
  </tbody>
</table>
</div>
  @if($ff_data['id'])
  <form method="POST" action="/admin/forfaits/loadComment">
    <input type="hidden" id="item_id" name="item_id" value="{{$ff_data['id']}}">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label for="ff_comments">Agregar comentarios / info</label>
      <textarea class="form-control" id="more_info" name="more_info" rows="3">{{$ff_data['more_info']}}</textarea>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary" id="sendComments">Guardar</button>
    </div>
  </form>
  @endif
<div class="text-center">
  <h3><a href="https://miramarski.com/forfait-new/{{encriptID($book->id)}}-{{encriptID($customer->id)}}" target="black">Pagina Forfaits >> </a></h3>
</div>
  @if($ff_data['id'] && $ff_data['ffexpr_status'] != 1)
  <form method="POST" action="/admin/forfaits/sendBooking">
    <input type="hidden" id="item_id" name="item_id" value="{{$ff_data['id']}}">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="text-center">
      <p>Solicitar la reserva del forfaits en ForfaitsExpress</p>
      <button type="submit" class="btn btn-primary" id="sendComments">enviar</button>
    </div>
  </form>
  @endif


<div class="cart-container">
<H2>FORFAIT</H2>
<div class="table-responsive">
  <table class="table">
  <thead>
    <tr>
      <th scope="col">Edad</th>
      <th scope="col">Tarifa</th>
      <th scope="col">Días</th>
      <th scope="col">Inicio</th>
      <th scope="col">Fin</th>
      <th scope="col" class="text-right">Precio</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($ff_data['forfait_data']))
    @foreach($ff_data['forfait_data'] as $item)
    <tr>
       <td>{{$item->age}}</td>
       <td>{{$item->typeTariffName}}</td>
      <td>
        {{$item->days}}<br/>
      </td>
      <td>{{$item->dateFrom}}</td>
      <td>{{$item->dateTo}}</td>
      <th scope="row" class="text-right">{{$item->price}}€</th>
    </tr>
    @endforeach
    @endif
  </tbody>
  <tfoot>
    <tr class="spacer">
      <th colspan="5">Subtotal</th>
      <th scope="row" class="text-right">{{$ff_data['forfait_total']}}€</th>
    </tr>
  </tfoot>
</table>
</div>


<H2>ALQUILER DE MATERIALES</H2>
<div class="table-responsive">
  <table class="table">
  <thead>
    <tr>
      <th scope="col">Cant</th>
      <th scope="col">Nombre</th>
      <th scope="col">Días</th>
      <th scope="col">Inicio</th>
      <th scope="col">Fin</th>
      <th scope="col" class="text-right">Precio</th>
    </tr>
  </thead>
  @if(isset($ff_data['materials_data']))
  <tbody>
    @foreach($ff_data['materials_data'] as $item)
    <tr>
      <td>{{$item->nro}}</td>
      <td class="item_name">{{$item->item->name}} - {{$item->item->type}}
        <div class="item_detail">
        {!!$item->item->equip!!}
        <hr>
        {!!$item->item->class!!}
        </div>
      </td>
      <td>{{$item->total_days}}</td>
      <td>{{date('d/m/Y',strtotime($item->date_start))}}</td> 
      <td>{{$item->date_end}}</td>
      <th scope="row" class="text-right">{{$item->total}}€</th>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="spacer">
      <th colspan="5">Subtotal</th>
      <th scope="row" class="text-right">{{$ff_data['materials_total']}}€</th>
    </tr>
  </tfoot>
  @endif
  
</table>
</div>
<H2>CLASES</H2>
<div class="table-responsive">
  <table class="table">
  <thead>
    <tr>
      <th scope="col">Cant</th>
      <th scope="col">Nombre</th>
      <th scope="col">Cursado</th>
      <th scope="col">Idioma</th>
      <th scope="col">Nivel</th>
      <th scope="col" class="text-right">Precio</th>
    </tr>
  </thead>
  @if(isset($ff_data['classes_data']))
  <tbody>
    @foreach($ff_data['classes_data'] as $item)
    <tr >
      <td>{{$item->nro}}</td>
      <td class="item_name">{{$item->item->name}} - {{$item->item->type}}
        <div class="item_detail">
        {!!$item->item->equip!!}
        <hr>
        {!!$item->item->class!!}
        </div>
      </td>
      <td> 
        {{date('d/m/Y',strtotime($item->date_start))}}<br/>
        @if($item->start>0)
        <div>Inicio {{$item->start}}:00 Hrs | {{$item->hours}} Horas</div>
        @else
        {{date('d/m/Y',strtotime($item->date_end))}} ({{$item->total_days}} Dias)
        @endif
      </td>
      <td>{{$item->language}}</td>
      <td>{{$item->level}}</td>
      <th scope="row" class="text-right">{{$item->total}}€</th>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th colspan="5">Subtotal</th>
      <th scope="row" class="text-right">{{$ff_data['classes_total']}}€</th>
    </tr>
  </tfoot>
  @endif
</table>
</div>
</div>
<script>
$( document ).ready(function() {
  $( "td.item_name" ).hover(function() {$( this ).find( '.item_detail' ).show();},function() {$( this ).find( '.item_detail' ).hide();});
});
  
  </script>
@endsection