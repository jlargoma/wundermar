     @if($oInvoice->id>0)
    <div class="col-xs-12 text-left">
      <p>Núm de Factura: <b>{{$oInvoice->num}}</b></p>
      <p>Fecha de Emisión: <b>{{convertDateToShow_text($oInvoice->date)}}</b></p>
    </div>
    @endif
    
    <form action="{{ route('invoice.save') }}" method="post" id="sendInvoiceBook">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" value="{{$oInvoice->id ?? null}}">
      <input type="hidden" name="book_id" value="{{$book_id}}">
      <input type="hidden" name="confirm" value="1">
    
      
      <h3 class="row col-xs-12 invoice">Emisor:</h3>
      <div class=" row col-xs-12 bg-white mb-1em">
        <select name="emisor" class="form-control">
          <option value="">--</option>
          @if($emisores)
          @foreach($emisores as $k=>$item)
          <option value="{{$k}}" <?php echo ($emisor == $k) ? 'selected':''; ?>>{{$item['name']}}</option>
          @endforeach
          @endif
        </select>
      </div>
      <h3 class="row col-xs-12 invoice">Cliente:</h3>
      <div class="row col-xs-12 bg-white">
          <div class="col-md-4 col-xs-12 push-20">
            <label for="">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{$oInvoice->name ?? ''}}">
          </div>
          <div class="col-md-4 col-xs-12 push-20">
            <label for="">CIF/NIF/DNI/NIE</label>
            <input type="text" name="nif" class="form-control" value="{{$oInvoice->nif ?? ''}}">
          </div>
          <div class="col-md-4 col-xs-12 push-20">
            <label for="">Email</label>
            <input type="text" name="email" class="form-control" value="{{$oInvoice->email ?? ''}}">
          </div>
          <div class="col-md-4 col-xs-12 push-20">
            <label for="">Dirección</label>
            <input type="text" name="address" class="form-control" value="{{$oInvoice->address ?? ''}}">
          </div>
          <div class="col-md-4 col-xs-12 push-20">
            <label for="">Telefono</label>
            <input type="text" name="phone" class="form-control" value="{{$oInvoice->phone ?? ''}}">
          </div>
        </div>
        <h3 class="row col-xs-12 invoice">Items: <button class="btn pull-right" type="button" id="addItem" >+Item</button></h3>
        
        <div class="row col-xs-12">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th width="65%">Item</th>
                  <th class="text-center" width="5%">% IVA</th>
                  <th class="text-center">Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="itemInvoices">
                <?php 
                  if($items):
                    foreach($items as $item):
                    ?>
                  <tr>
                    <td><textarea name="item[]" class="form-control itemname">{{$item['detail']}}</textarea></td>
                    <td><input type="number" step="0.01" name="iva[]" class="form-control iva" value="{{$item['iva']}}"></td>
                    <td><input type="number" step="0.01" name="price[]" class="form-control prices"  value="{{$item['price']}}"></td>
                    <td><button type="button" class="rmItem">X</button></td>
                  </tr>                    
                    <?php
                    endforeach;
                  else:
                  ?>
                  <tr>
                    <td><textarea type="text" name="item[]" class="form-control itemname"></textarea></td>
                    <td><input type="number" step="0.01" name="iva[]" class="form-control iva"></td>
                    <td><input type="number" step="0.01" name="price[]" class="form-control prices"></td>
                    <td><button type="button" class="rmItem">X</button></td>
                  </tr>
                  <?php
                  endif;
                  ?>
              </tbody>
               <tfoot id="summary"></tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="row col-xs-12  text-center">
        
        <button class="btn btn-complete" type="submit" >Guardar</button>
        @if($oInvoice->id>0)
        <button class="btn btn-danger" type="button" id="delete" data-id="{{$oInvoice->id}}" >Eliminar</button>
        <a href="{{ route('invoice.downl',$oInvoice->id) }}" class="btn btn-success"><i class="fa fa-download"></i></a>
        @endif
      </div>
    </form>

@include('backend.invoices.forms._form-script')
<style>
  h3#modalSafetyBox_title {
    float: left;
    margin-top: -29px;
}
</style>
  