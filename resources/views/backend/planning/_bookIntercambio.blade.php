<script type="text/javascript">
  $(document).ready(function () {

    var intercambio = {
      'book_1': null,
      'book_2': null,
    };
  
    $(document).on('keyup', '#s_booking_1', function () {
      var value = $(this).val();

      if (value.length < 3 && value.length != 0) {
        return false;
      }
      if (value) {
        $('#lst_interc_1').load('/admin/reservas/api/intercambio-search/1/' + value);
      }
    });

    $(document).on('click', '.item_interc_1', function () {
      var that = $(this);
      $('.item_interc_1').removeClass('active');
      that.addClass('active');
      intercambio.book_1 = that.data('id');
    });
    
    $(document).on('keyup', '#s_booking_2', function () {
      var value = $(this).val();

      if (value.length < 3 && value.length != 0) {
        return false;
      }
      if (value) {
        $('#lst_interc_2').load('/admin/reservas/api/intercambio-search/2/' + value);
      }
    });

    $(document).on('click', '.item_interc_2', function () {
      var that = $(this);
      $('.item_interc_2').removeClass('active');
      that.addClass('active');
      intercambio.book_2 = that.data('id');
    });

    $('#sendIntercambio').on('click',function(){
      
      if (!intercambio.book_1 || !intercambio.book_2){
        $('#IntercambioResponse').show().removeClass('alert-success').addClass('alert-warning').text('Debe seleccionar ambas reservas');
        return '';
      }
      
      if (intercambio.book_1 == intercambio.book_2){
        $('#IntercambioResponse').show().removeClass('alert-success').addClass('alert-warning').text('Ambas reservas no pueden ser la misma');
        return '';
      }
      
      var url = '/admin/reservas/api/intercambio-change/';
      
       $.ajax({
            type: "POST",
            url: '/admin/reservas/api/intercambio-change',
            data: {_token : "{{csrf_token()}}",book_1:intercambio.book_1,book_2:intercambio.book_2},
            dataType:'json',
            success: function(data){
              $('#IntercambioResponse').show();
              if (data.status == 'ok'){
                $('#IntercambioResponse').removeClass('alert-warning').addClass('alert-success').text('Reservas Intercambiadas');
                setTimeout(function(){window.location.reload();},1000)
//               
              } else {
                $('#IntercambioResponse').removeClass('alert-success').addClass('alert-warning').text(data.msg);
              }
            },
            error: function(response){
              $('#IntercambioResponse').show();
               $('#IntercambioResponse').removeClass('alert-success').addClass('alert-warning').text('Error de sistema');
                    console.log(response,'console');
            }
        });
       
    });


  });

</script>
<style>
  .box-intercambio{
        box-shadow: 1px 1px 7px #868585;
    padding: 12px;
    margin-top: 1em;
  }
  #lst_interc_2 ul,
  #lst_interc_1 ul {
    padding: 12px 00;
  }
  div#lst_interc_2,
  div#lst_interc_1 {
    height: 24em;
    overflow-y: auto;
  }
  #lst_interc_2 ul li,
  #lst_interc_1 ul li{
    list-style: none;
    border-bottom: 1px solid #dadada;
    padding: 5px;
    cursor: pointer;
  }

  #lst_interc_2 ul li.active,
  #lst_interc_1 ul li.active {
    background-color: #d8ceff;
    color: #000;
  }
  #sendIntercambio{
    margin: 1em auto;
  }
  #lst_interc_2 ul li b,
  #lst_interc_1 ul li b {
    background-color: #828282;
    padding: 3px 6px;
    color: #fff;
    margin-right: 4px;
  }
</style>

<div class="box-intercambio">
  <h2>Intercambio de habitaciones</h2>

<div class="row">
  <div class="col-md-6">
    <div class="">
      <label>Reserva 1</label>
      <input type="search" name="s_booking_1" id="s_booking_1" class="form-control">
      <div id="lst_interc_1">

      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="">
      <label>Reserva 2</label>
      <input type="search" name="s_booking_2" id="s_booking_2" class="form-control">
      <div id="lst_interc_2">

      </div>
    </div>
  </div>
  <div class="col-md-12 text-center">
    <button type="button" class="btn btn-primary" id="sendIntercambio">Intercambiar</button>
    <p class="alert alert-warning" style="display:none;" id="IntercambioResponse"></p>
  </div>
</div>
  </div>