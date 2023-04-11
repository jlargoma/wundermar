<div class="row" id="content-form-create-book">
    <div class="col-md-12">
		<?php if (count($rooms) > 0):?>
		<?php foreach ($rooms as $key => $room): ?>
        <div class="col-md-4">
            <h2 class="text-left">
                {{ $room->name }}
            </h2>
            <p class="text-justify">
                {{ $room->description }}
            </p>
			<?php $nights = $start->copy()->diffInDays($finish); ?>
			<?php $luxury = ($room->luxury != 0) ? $room->luxury : 2; ?>
			<?php $priceBook = \App\Http\Controllers\BookController::getPriceBook($start->copy()
			                                                                            ->format('d/m/Y'), $finish->copy()
			                                                                                                      ->format('d/m/Y'), $pax, $room->id); ?>
			<?php $pricePark = \App\Http\Controllers\BookController::getPricePark(1, $nights); ?>
			<?php $priceLuxury = \App\Http\Controllers\BookController::getPriceLujo($luxury); ?>
            <div class="col-md-12">
                <p class="text-left">
                    <b><span class="font-s30 font-w800">Datos de la reserva:</span></b><br>
                    Estancia: <b>{{ $nights }} @if($nights == 1)Noche @else Noches @endif </b><br>
                    Reserva: <b>{{ $priceBook }}€</b> <br>
                    Parking: <b>{{ $pricePark }}€</b> <br>
                    Lujo: <b>{{ $priceLuxury }}€</b> <br>
                </p>

                <h3 class="text-center">
                    Total de la reserva: <b>{{ $priceBook + $pricePark + $priceLuxury }}€</b>
                </h3>
                <div class="col-md-12 text-center">
                    @if($instantPayment)
                        <button class="btn btn-primary font-w300">Reserva Inmediata</button>
                    @else
						<?php $start1 = \Carbon\Carbon::createFromFormat('Y-m-d', $start->copy()->format('Y-m-d'))
						                              ->format('d M, y');
						$finish1 = \Carbon\Carbon::createFromFormat('Y-m-d', $finish->copy()->format('Y-m-d'))
						                         ->format('d M, y');
						?>

                        <form action="{{ route('book.create') }}" method="post" id="form-create-book">
                            <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" id="newroom" name="newroom" value="{{ $room->id }}">
                            <input type="hidden" id="fechas" name="fechas"
                                   value="<?php echo $start1;?> - <?php echo $finish1 ?>">
                            <input type="hidden" id="from" name="from" value="1">
                            <input type="hidden" id="name" name="name" value="{{ $name }}">
                            <input type="hidden" id="email" name="email" value="{{ $email }}">
                            <input type="hidden" id="phone" name="phone" value="{{ $phone }}">
                            <input type="hidden" id="dni" name="dni" value="{{ $dni }}">
                            <input type="hidden" id="type_park" name="type_park" value="1">
                            <input type="hidden" id="type_luxury" name="type_luxury" value="1">
                            <input type="hidden" id="pax" name="pax" value="{{ $pax }}">
                            <input type="hidden" id="agency" name="agency" value="0">
                            <input type="hidden" id="nigths" name="nigths" value="{{ $nights }}">
                            <button class="btn btn-primary font-w300" type="submit">Solicitar Reserva</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
		<?php endforeach; ?>
		<?php else: ?>
        <h2 class="text-center">
            No hay Habitaciones disponibles
        </h2>
		<?php endif; ?>
    </div>
</div>
<script>
  $('#form-create-book').submit(function (event) {
    event.preventDefault();
    var url = $(this).attr('action');
    var _token = $('#form-create-book #_token').val();
    var newroom = $('#form-create-book #newroom').val();
    var fechas = $('#form-create-book #fechas').val();
    var from = $('#form-create-book #from').val();
    var name = $('#form-create-book #name').val();
    var email = $('#form-create-book #email').val();
    var phone = $('#form-create-book #phone').val();
    var dni = $('#form-create-book #dni').val();
    var pax = $('#form-create-book #pax').val();
    var agency = $('#form-create-book #agency').val();
    var type_park = $('#form-create-book #type_park').val();
    var type_luxury = $('#form-create-book #type_luxury').val();
    var nigths = $('#form-create-book #nigths').val();
    $.post(url, {
      _token: _token,
      newroom: newroom,
      fechas: fechas,
      from: from,
      name: name,
      email: email,
      phone: phone,
      dni: dni,
      parking: type_park,
      type_luxury: type_luxury,
      pax: pax,
      agency: agency,
      nigths: nigths,
    }).done(function (data) {
      //$('#content-form').hide();
      $('#content-form-create-book').empty().append(data);
    });
  });
</script>