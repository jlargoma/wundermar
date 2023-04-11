
<div class="modal fade slide-up in" id="modal_bloqueo" tabindex="-1" role="dialog" aria-hidden="true" style=" z-index: 9999;">
    <div class="modal-dialog modal-xd">
        <div class="modal-content-classic">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
                <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
            </button>
            <h3 id="modal_bloqueo_title">Bloqueo de Apartamentos</h3>
            <div class="row" id="modal_bloqueo_content" style="margin-top:1em;">

                <form method="post" style="clear:both;" action="/admin/limpieza/bloquear">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                        <div class="col-md-5 col-xs-8 push-xs-10">
                            <label>Entrada</label>
                            <div class="">
                                <input type="text" class="form-control daterange02" id="fechas" name="fechas" required="" >
                                <input type="hidden" class="date_start" id="start" name="start">
                                <input type="hidden" class="date_finish" id="finish" name="finish">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-4 push-xs-10">
                            <label>Noches</label>
                            <input type="text" class="form-control nigths" name="nigths" style="width: 100%" disabled >
                        </div>
                        <div class="col-md-4 col-xs-12 push-xs-10">
                            <label>Apartamento</label>
                            <select class="form-control full-width newroom minimal" name="newroom" id="newroom" required>
                                <option ></option>
                                <?php foreach ($rooms as $room): ?>
                                    <?php if ($room->state > 0): ?>
                                        <option value="<?php echo $room->id ?>" data-luxury="<?php echo $room->luxury ?>" data-size="<?php echo $room->sizeApto ?>" <?php
                                        if (isset($data['newRoomID']) && $data['newRoomID'] == $room->id) {
                                            echo 'selected';
                                        }
                                        ?>>
                                        <?php echo substr($room->nameRoom . " - " . $room->name, 0, 12) ?>
                                        </option>
    <?php endif; ?>
<?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-xs-12 push-xs-10 mt-1em text-center">
                            <label>Comentarios Internos</label>
                            <textarea class="form-control book_comments" name="book_comments" rows="5" > </textarea>
                        </div>
                        <div class="col-xs-12 push-xs-10 mt-1em text-center">
                            <button class="btn btn-success" >Enviar</button>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>
<?php $t_class = ($isMobile) ? '' : 'th-bookings'; ?>
<div class="modal fade slide-up in" id="modalNextsExtrs" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-md">
        <div class="modal-content-wrapper">
            <div class="modal-content" style="padding: 7px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
                    <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
                </button>
                <h3 id="modal_bloqueo_title">Extras de reservas</h3>
                <div class="blockTableAlert">
                        <table class="table tableAlert table-data  table-striped" >
                            <thead>
                                <tr class ="text-center text-white" style="background-color: #448eff;">
                                    <th class="{{$t_class}} th-name" >Cliente</th>
                                    <th class="th-bookings"> 
                                        @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
                                    </th>
                                    <th class="{{$t_class}} th-6">Apart</th>
                                    <th class="{{$t_class}} th-4">IN</th>
                                    <th class="{{$t_class}} th-4">OUT</th>
                                    <th class="{{$t_class}} th-2">Desayuno</th>
                                    <th class="{{$t_class}} th-2">Parking</th>
                                    <th class="{{$t_class}} th-2">Excursiones</th>
                                    <th class="{{$t_class}} th-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lstExtrs as $e)
                                <tr>
                                    <td>{{$e->cli}}</td>
                                    <td>{{$e->phone}}</td>
                                    <td>{{$e->room}}</td>
                                    <td>{{$e->in}}</td>
                                    <td>{{$e->out}}</td>
                                    <td><?php echo ($e->breakfast) ? '<i class="fas fa-coffee"></i> ' . $e->breakfast : '-' ?></td>
                                    <td><?php echo ($e->parking) ? '<i class="fas fa-parking"></i> ' . $e->parking : '-' ?></td>
                                    <td><?php echo ($e->excursion) ? '<i class="fas fa-guitar"></i> ' . $e->excursion : '-' ?></td>
                                    <td>
                                        <button data-id="{{$e->bID}}" 
                                                data-delivered="{{$e->delivered}}"
                                                class="btn btn-xs btn-default toggleDeliver" 
                                                type="button" 
                                                data-toggle="tooltip" title="" 
                                                data-original-title="Activa / Desactiva Alerta de Entrega" 
                                                >
                                            @if($e->delivered == 1)
                                            <i class="fa fa fa-bell-slash" aria-hidden="true"></i>
                                            @else
                                            <i class="fa fa-bell" aria-hidden="true"></i>
                                            @endif
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>