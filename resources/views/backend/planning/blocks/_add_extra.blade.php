<div class="extras">
  <div class="extras_tit">
    @if($totalExtras>0)
    <span>
    <i class="fa fa-list"></i>&nbsp;Total Supl. Extras <span id="extra_total">{{$totalExtras}}</span> €   
    </span>
    @else
    &nbsp;Supl. Extras 
    @endif
  </div>
  <div class="extras_item row">
      <div class="col-xs-4">Nombre</div>
      <div class="col-xs-2">Qty</div>
      <div class="col-xs-2">PVP</div>
      <div class="col-xs-3">Vendor</div>
      <div class="col-xs-2"></div>
    </div>
  <div id="extras_list">
    
  </div>
  <hr>
  <div class="extras_item newItem ">
    <div class="row">
      <input type="hidden" id="extra_price_orig" value="">
      <input type="hidden" id="extra_cost_orig" value="">
      <div class="col-xs-4 mb-1em">
        <select class="extra_type form-control">
          <option value="">--</option>
          @foreach($extras as $e)
          <option value="{{$e->id}}">{{$e->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-xs-2 mb-1em"><input type="number" class="form-control extra_qty" name="qty"></div>
      <div class="col-xs-2 mb-1em input-price">
        <label>€</label>
        <input type="number" class="form-control extra_price" name="price">
      </div>
      <div class="col-xs-3  mb-1em">
        <select class="extra_vdor form-control">
          <option value="">--</option>
          @foreach(\App\ExtraPrices::getVendrs() as $k=>$v)
          <option value="{{$k}}">{{$v}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-4 mt-1em mb-1em">
        Generar registro de cobro:
      </div>
      <div class="col-xs-4  mb-1em">
        <select class="extra_status form-control">
          <option value="NO">No Generar</option>
          <option value="CASH">Cobrado CASH</option>
          <option value="TPV">Cobrado TPV</option>
        </select>
      </div>
      <div class="col-xs-2">
        <button class="save action btn " type="button">
        <i class="fa fa-save " title="Guardar"></i> Guardar
        </button>
      </div>
      <div class="col-xs-12 text-left">
        <small><b>Qty:</b> cantidad por días de estadía. Ej: 2 personas x 3 noces = 6 desayunos</small>
      </div>
    </div>
  </div>
  <a href="{{$linksExtr}}" target="_blank" title="Comprar extras">Comprar extras</a>
</div>


<script type="text/javascript">
  $(document).ready(function () {


    var LoadExtraPrices =  function(){
      $.get('/admin/reservas/api/get-all-extras/{{$book->id}}', function (data) {
        $('#extra_total').html(data.total);
        $('#extras_list').html(data.content);
      });
    }
    
    LoadExtraPrices();
    
    $('#extras_list').on('click', '.btn-delete-extr', function () {
      if (confirm('Eliminar el extra?')){
        var obj = $(this).closest('.extras_item');
        updateExtra(obj,'BORRAR');
      }
    });
    $('#extras_list').on('change', '.extras_val', function () {
       var obj = $(this).closest('.extras_item');
       updateExtra(obj);
    });
    
    var updateExtra = function(obj,status=''){
      var data = {
          _token: "{{ csrf_token() }}",
          bID : {{ $book->id }},
          id : obj.data('id'),
          type : obj.find('.extra_type').val(),
          qty : obj.find('.extra_qty').val(),
          price : obj.find('.extra_price').val(),
          cost : obj.find('.extra_cost').val(),
          vdor : obj.find('.extra_vdor').val(),
          status : status,
         }
         
      $.post('/admin/reservas/api/updateExtra', data, function (data) {
        if (data == 'ok'){
          LoadExtraPrices();
          window.show_notif('OK','success','Registro Actualizado.');
          location.reload();
//          window.getPayments();
        } else {
          window.show_notif('ERROR','danger','Registro no encontrado');
        }

      });
    }
    
    
    $('.extras_item.newItem').on('change', '.extra_type', function () {
      var id = $(this).val();
      var that = $(this);
      $.post('/admin/reservas/api/getExtra', {_token: "{{ csrf_token() }}", id: id}, function (data) {
        that.closest('.extras_item').find('.extra_price').val(data.p);
        that.closest('.extras_item').find('.extra_cost').val(data.c);
        that.closest('.extras_item').find('.extra_qty').val(1);

        $('#extra_price_orig').val(data.p);
        $('#extra_cost_orig').val(data.c);
      
      });
    });
    
    $('.extras_item.newItem').on('change', '.extra_qty', function () {
      var that = $(this).closest('.extras_item')
      var value = $(this).val();
      if (value>0){
        var val = $('#extra_price_orig').val();
        that.find('.extra_price').val(val*value);

        var val = $('#extra_cost_orig').val();
        that.find('.extra_cost').val(val*value);
      } else {
        $(this).val(1);
      }
    });
    $('.extras_item.newItem').on('click', '.save', function () {
      var obj = $(this).closest('.extras_item')
      
      var data = {
        _token: "{{ csrf_token() }}",
        bID : {{ $book->id }},
        type : obj.find('.extra_type').val(),
        qty : obj.find('.extra_qty').val(),
        price : obj.find('.extra_price').val(),
        cost : obj.find('.extra_cost').val(),
        status : obj.find('.extra_status').val(),
        vdor : obj.find('.extra_vdor').val(),
        status : obj.find('.extra_status').val(),
       }
      $.post('/admin/reservas/api/setExtra', data, function (data) {
        if (data == 'ok'){
          LoadExtraPrices();
          
          obj.find('.extra_type').val('');
          obj.find('.extra_qty').val('');
          obj.find('.extra_price').val('');
          obj.find('.extra_status').val('');
          window.show_notif('OK','success','Registro Creado.');
          location.reload();
//          window.calculate();
//          window.getPayments();
        } else {
          window.show_notif('ERROR','danger','Registro no creado');
        }
        
      });
    });
    
    $('.extras_tit').on('click',function(){
      if ($(this).hasClass('open')){
        $(this).removeClass('open');
        $('#extras_list').empty();
      } else {
        $(this).addClass('open');
        LoadExtraPrices()
      }
    })
  });
</script>
