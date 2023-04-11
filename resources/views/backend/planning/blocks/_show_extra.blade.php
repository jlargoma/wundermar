<div class="extras" style="display:none;">
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
</div>


<script type="text/javascript">
  $(document).ready(function () {


    var LoadExtraPrices =  function(){
      $.get('/admin/reservas/api/get-all-extras/{{$book->id}}', function (data) {
        if (data.count>0) $('.extras').show();
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
