<?php $mobile = new \App\Classes\Mobile(); ?>

<link href="{{ asset('/frontend/hover.css')}}" rel="stylesheet" media="all">
<link rel="stylesheet" href="{{ asset('/css/components/radio-checkbox.css')}}" type="text/css" />

<div class="modal-header clearfix text-left">
    <div class="row">
        <div class="col-xs-12 bg-black push-20">
            <h4 class="text-center white">
              CALCULAR RESERVA
            </h4>
            <button type="button" class="close close-calculate" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
                <i class="pg-close fs-20" style="color: #e8e8e8;"></i>
            </button>
        </div>
    </div>
</div>

<div id="content-book" class="row clearfix push-10" >    
  <div class="col-xs-12 clearfix"  style="padding: 20px 0;">
    <div class="row">
      <div class="col-md-12">
        <div class="row" id="content-book-response">
          <div class="col-xs-12 front" >
            <div id="form-content">
              <form id="form-book-apto-lujo" action="{{url('/admin/reservas/help/getTotalBook')}}" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" id="cr_id" value="{{$cr_id}}">
                <input type="hidden" name="email" id="email" value="{{$email}}">
                <input type="hidden" name="phone" id="phone" value="{{$phone}}">

                <div class="col-md-12">
                  <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6 white">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" name="name" id="cal-nombre" placeholder="Nombre..." maxlength="40" aria-label="Escribe tu nombre" value="{{$name}}">
                  </div>
                  <div class="form-group col-sm-12 col-xs-6 col-md-6 white">
                    <label for="date" style="display: inherit!important;">*Entrada - Salida</label>
                    <input type="text" class="form-control daterange1" id="date"   name="date" required style="cursor: pointer;text-align: center;" readonly="" value="{{$date}}">
                    <p  class="help-block min-days" style="display:none;line-height:1.2;color:red;">
                      <b>* ESTANCIA MÍNIMA: 2 NOCHES</b>
                    </p>
                  </div>
                  <div class="hidden-xs hidden-sm" style="clear: both;"></div>
                  <div class="form-group col-sm-12 col-xs-4 col-md-4 not-padding-mobile">
                    <label>Edificio</label>
                    <select class="form-control minimal" id="site_id">
                      <option value="0">Todos</option>
                      <?php foreach (\App\Sites::all() as $item): ?>
                        <option value="<?php echo $item->id ?>" <?php echo ($item->id==$site_id) ? 'selected' : '' ?>>
                          <?php echo $item->name ?>
                        </option>
                      <?php endforeach ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-12 col-xs-4 col-md-2 white">
                    <label for="quantity" style="display: inherit!important;">*Personas</label>
                    <div class="quantity center clearfix divcenter">
                      <select id="quantity" class="form-control minimal" name="quantity">
                        <?php for ($i = 1; $i <= 14; $i++): ?>
                        <option value="<?php echo $i ?>" <?php echo ($i==$pax) ? 'selected' : '' ?>><?php echo $i ?></option>  
                        <?php endfor ?>
                      </select>
                    </div>
                    <p class="help-block hidden-sm hidden-xs" style="line-height:1.2">Máx 12</p>
                  </div>
                  <div class="form-group col-sm-12 col-xs-6 col-md-6">
                    <label>Tipo Apto</label>
                    <select class="form-control minimal" id="size_apto_id">
                      <option value="0">Todos</option>
                      <?php foreach (\App\SizeRooms::allSizeApto() as $k=>$v): ?>
                        <option value="{{$k}}">{{$v}}</option>
                      <?php endforeach ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-12 col-xs-12 col-md-12 col-lg-12 text-center">
                    <button type="submit" class="btn btn-success btn-cons btn-lg" id="confirm-reserva">Calcular reserva</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="col-xs-12 back" style="display: none;">

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<script type="text/javascript" src="{{asset('/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/js/form_booking.js')}}"></script>
<script type="text/javascript" src="{{asset('/js/components/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{ asset('/js/datePicker01.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#form-book-apto-lujo').submit(function(event) {

            event.preventDefault();

            var date = $('#date').val().trim().split(' - ');
            var start = new Date(date[0]);
            var end = new Date(date[1]);

            var _token   = $('input[name="_token"]').val();
            var name     = $('#cal-nombre').val();
            var email    = $('#email').val();
            var phone    = $('#phone').val();
            var date     = $('input[name="date"]').val();
            var quantity = $('select[name="quantity"]').val();
            var comment  = "";
            var site_id       = $('#site_id').val();
            var size_apto_id  = $('#size_apto_id').val();

            var url = $(this).attr('action');


             $.post( url , {_token : _token,  name : name,  email : email,
              phone : phone,
              start : start.yyyymmmdd(),end : end.yyyymmmdd(),
              quantity : quantity,
              site_id : site_id, size_apto : size_apto_id}, function(data) {  
                $('#content-book-response .back').empty();
                $('#content-book-response .back').append(data);

                $("#content-book-response .front").hide();

                $("#content-book-response .back").show();
                

            });

        });
        
        $("#content-book-response").on('click','.backBooking',function(){
          $('#content-book-response .back').empty();
          $("#content-book-response .back").hide();
          $("#content-book-response .front").show();

        });
        
        $('#site_id').on('change',function(){
          var id = $(this).val();
          $("#size_apto_id").load('/ajax/get-size-site/'+id,);
        });
        
    });

</script>   
