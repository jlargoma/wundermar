<?php   use \Carbon\Carbon;
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 
<link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
    table.requests td,th{
        padding: 2px 4px 2px 4px !important;
        font-size: 12px;
    }
    
    img.action_icon{
        float:left;
        margin:2px;
    }
    
    th.fix_width{
         width: 70px;
    }
    
    td.fix_width{
        width: 65px;
    }
    
    td.fix_width span{
        padding: 0;
    }
    
    th.pan{
        width: 120px;
    }
    
    tr:hover {
        background-color: #E4E4E4;
    }

    tr:hover td {
        background-color: transparent; /* or #000 */
    }

</style>
@endsection

@section('content')
<?php
    if (!isset($jsStripe)): ?>
        <script src="//js.stripe.com/v3/"></script>
<?php endif ?>
        
<?php 
    $stripe = App\Http\Controllers\StripeController::$stripe;
    $payments = App\Http\Controllers\FortfaitsController::getRequestsPayments();
?>
            
<div class="container-fluid padding-25 sm-padding-10 bg-white">
    <div class="container clearfix col-lg-12">
        <div class="col-lg-5 row">
            <button class="btn btn-success btn-cons" type="button" id="stripePayment">
                <i class="fa fa-money" aria-hidden="true"></i> <span class="bold">Ver tarifas</span>
            </button>
            <button class="btn btn-success btn-cons" type="button" id="stripePayment">
                <i class="fa fa-money" aria-hidden="true"></i> <span class="bold">Cobros stripe</span>
            </button>
        </div>
        <div class="col-lg-7">
            <table class="table-bordered table-condensed requests col-lg-5">
                <thead>
                    <tr>
                        <th>MODIFICAR</th>
                        <th>VALOR</th>
                        <th>TIPO</th>
                        <th>TOTAL</th>
                        <th>COMISIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Http\Controllers\FortfaitsController::getCommissions() as $commission)
                        <tr>
                            <td class="text-center"><button class="btn btn-primary btn-xs updateCommission" data-id="{{$commission->id}}">Modificar</button></td>
                            <td><span class="commission_value">{{round($commission->value,0)}}</span>%</td>
                            <td>{{$commission->type}}</td>
                            <td><span class="payment_total_{{$commission->id}}">{{$payments[$commission->id]['total']}}</span>€</td>
                            <td><span class="payment_commission_{{$commission->id}}">{{$payments[$commission->id]['commissioned']}}</span>€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="container clearfix col-lg-12">

        <table class="table-bordered table-condensed requests">
           <thead>
             <tr>
                <th rowspan="2"></th>
                <th rowspan="2">ID</th>
                <th rowspan="2">Nombre</th>
                <th rowspan="2">Email</th>
                <th rowspan="2">Teléfono</th>
                <th rowspan="2">Fecha Inicio</th>
                <th rowspan="2">Fecha Final</th>
                <th rowspan="2" class="pan">Tarjeta Crédito</th>
                <th rowspan="2">Estado</th>
                <th colspan="3">Forfaits</th>
                <th colspan="3">Material</th>
                <th colspan="3">Clases</th>
                <th rowspan="2">F. Solicitud</th>
                <th rowspan="2">Observaciones</th>
             </tr>
             <tr>
                 <th>Descripción</th>
                 <th class="fix_width">Importe</th>
                 <th class="fix_width">Acciones</th>
                 <th>Descripción</th>
                 <th class="fix_width">Importe</th>
                 <th class="fix_width">Acciones</th>
                 <th>Descripción</th>
                 <th class="fix_width">Importe</th>
                 <th class="fix_width">Acciones</th>
             </tr>
           </head>
           <tbody>
              @foreach($requests as $request)

                <?php 
                    if($request->request_prices != NULL){
                        $prices = unserialize($request->request_prices);
                    }else{
                        $prices = [];
                    }
                ?>

                 <tr>
                    <td><button class="btn btn-danger btn-xs bold deleteRequest" data-id="{{$request->id}}">X</button></td>
                    <td>{{$request->id}}</td>
                    <td>{{$request->name}}</td>
                    <td>{{$request->email}}</td>
                    <td>{{$request->phone}}</td>
                    <td>{{$request->start}}</td>
                    <td>{{$request->finish}}</td>
                    <td class="text-center">
                        <p class="pan">{{$request->cc_pan}}</p>
                        <button class="btn btn-primary btn-xs addPAN" data-id="{{$request->id}}">Modificar</button>
                    </td>
                    <td>
                        <span class="input-group-addon bg-transparent">
                            <input type="checkbox" class="estado" data-status="{{$request->status}}" data-id="{{$request->id}}" name="state" data-init-plugin="switchery" data-size="small" data-color="success" <?php echo ($request->status == 0) ? "" : "checked" ?>> 
                        </span>
                    </td>
                    <td colspan="3">
                        <table class="table-bordered col-lg-12">
                            <tbody>
                                @if($request->request_forfaits != NULL)
                                    <?php $stack = unserialize($request->request_forfaits); ?>
                                    @if(is_array($stack))
                                        @foreach($stack as $key => $item)
                                            <tr>
                                                <td>- {{$item}}</td>
                                                <td class="fix_width bold">
                                                    @if(isset($prices[$key]))
                                                        {{$prices[$key]}}€
                                                    @endif
                                                </td>
                                                <td class="fix_width">
                                                    <img class="action_icon" src="{{url('/img/sparrow_icon.png')}}"/>
                                                    <a href="#" class="launch_stripe"><img class="action_icon" src="{{url('/img/stripe_icon.png')}}"/></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                             </tbody>
                        </table>
                    </td>

                    <td colspan="3">
                        <table class="table-bordered col-lg-12">
                            <tbody>
                                @if($request->request_material != NULL)
                                    <?php $stack = unserialize($request->request_material); ?>
                                    @if(is_array($stack))
                                        @foreach($stack as $key => $item)
                                            <tr>
                                                <td>- {{$item}}</td>
                                                <td class="fix_width bold">
                                                    @if(isset($prices[$key]))
                                                        {{$prices[$key]}}€
                                                    @endif
                                                </td>
                                                <td class="fix_width">
                                                    <img class="action_icon" src="{{url('/img/sparrow_icon.png')}}"/>
                                                    <a href="#" class="launch_stripe"><img class="action_icon" src="{{url('/img/stripe_icon.png')}}"/></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </td>

                    <td colspan="3">
                        <table class="table-bordered col-lg-12">
                            <tbody>
                                 @if($request->request_classes != NULL)
                                    <?php $stack = unserialize($request->request_classes); ?>
                                    @if(is_array($stack))
                                        @foreach($stack as $key => $item)
                                            <tr>
                                                <td>- {{$item}}</td>
                                                <td class="fix_width bold">
                                                    @if(isset($prices[$key]))
                                                        {{$prices[$key]}}€
                                                    @endif
                                                </td>
                                                <td class="fix_width">
                                                    <img class="action_icon" src="{{url('/img/sparrow_icon.png')}}"/>
                                                    <a href="#" class="launch_stripe"><img class="action_icon" src="{{url('/img/stripe_icon.png')}}"/></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                             </tbody>
                        </table>
                    </td>

                    <td>{{$request->created_at}}</td>
                    <td>
                        <p class="comments">{{$request->comments}}</p>
                        <button class="btn btn-primary btn-xs addComments" data-id="{{$request->id}}">Modificar</button>
                    </td>
                 </tr>
              @endforeach
           </tbody>
        </table>

    </div>
</div>

<div id="stripe_div">
        <div class="col-lg-12" id="stripe-conten-index" style="">
        @include('backend.stripe.link')
        @include('backend.stripe.stripe', ['bookTocharge' => null])
    </div>
</div>

<script type="text/javascript" src="{{asset('/forfait/js/bootbox.min.js')}}"></script>
    
<script type="text/javascript">
    $(window).load(function(){
        $('span.switchery').on('click',function(){
            span = $(this);
            span_status = span.prev('input.estado');
            span_status.attr('data-status',span_status.attr('data-status') == 0 ? 1 : 0);

            request_id = span.prev('input[type="checkbox"]').attr('data-id');
            status = span_status.attr('data-status');

            $.ajax({
//                headers: {
//                    'X-CSRF-TOKEN': 
//                },
                type: "POST",
                url: "/ajax/forfaits/updateRequestStatus",
                data: {request_id:request_id,status:status},
                dataType:'json',
//                    async: false,
                success: function(response){
                    if(response === true){

                        $.ajax({
                            type: "POST",
                            url: "/ajax/forfaits/updatePayments",
//                                data: {},
                            dataType:'json',
//                                async: false,
                            success: function(response){
                                if(response !== false){
                                    $('span.payment_total_1').text(response[1].total);
                                    $('span.payment_commission_1').text(response[1].commissioned);
                                    $('span.payment_total_2').text(response[2].total);
                                    $('span.payment_commission_2').text(response[2].commissioned);
                                }else{
                                    bootbox.alert({
                                        message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El TOTAL/COMISIÓN no ha podido ser actualizado.<br/>Contacte con el administrador.</div>',
                                        backdrop: true
                                    });
                                }
                            },
                            error: function(response){
                                bootbox.alert({
                                    message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El TOTAL/COMISIÓN no ha podido ser actualizado.<br/>Contacte con el administrador.</div>',
                                    backdrop: true
                                });
                            }
                        });

                    }else if(response === 'false'){
                        bootbox.alert({
                            message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El Estado no ha sido guardado.<br/>Contacte con el administrador.</div>',
                            backdrop: true
                        });
                    }
                },
                error: function(response){
                    bootbox.alert({
                        message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El Estado no ha sido guardado.<br/>Contacte con el administrador.</div>',
                        backdrop: true
                    });
                }
            });
        });
    });

    $('button.deleteRequest').click(function(){
        request_id = $(this).attr('data-id');
        
        bootbox.confirm({
            message: '<div style="margin-top:10px">Está seguro que desea <strong>ELIMINAR</strong> esta Solicitud?</div>',
            buttons: {
                confirm: {
                    label: 'OK',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result === true){
                    url = "{{url('/admin/forfaits/deleteRequest')}}/"+request_id
                    window.location.replace(url);
                }
            }
        }); 
    });
    
    $('button.addPAN').click(function(){
        button = $(this);
        cc_p = button.prev('p.pan');
        bootbox.prompt({
            title: "Añadir PAN",
            value: cc_p.text(),
            callback: function(result) {
                if(result != null){
                    pan = result.replace(/\s/g,'');
                }else{
                    pan = '';
                }

                cc_p.text(pan);
                request_id = button.attr('data-id');

                $.ajax({
    //                headers: {
    //                    'X-CSRF-TOKEN': 
    //                },
                    type: "POST",
                    url: "/ajax/forfaits/updateRequestPAN",
                    data: {request_id:request_id,pan:pan},
                    dataType:'json',
                    async: false,
                    success: function(response){
                        if(response === 'false'){
                            bootbox.alert({
                                message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El PAN no ha sido guardado.<br/>Contacte con el administrador.</div>',
                                backdrop: true
                            });
                        }
                    },
                    error: function(response){
                        bootbox.alert({
                            message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El PAN no ha sido guardado.<br/>Contacte con el administrador.</div>',
                            backdrop: true
                        });
                    }
                });
            }
        });
    });
    
    $('button.addComments').click(function(){
        button = $(this);
        comments_p = button.prev('p.comments');
        bootbox.prompt({
            title: "Añadir Comentarios",
            value: comments_p.text(),
            callback: function(result) {
                if(result != null){
                    comments = result.replace(/\s/g,'');
                }else{
                    comments = '';
                }

                comments_p.text(comments);
                request_id = button.attr('data-id');

                $.ajax({
    //                headers: {
    //                    'X-CSRF-TOKEN': 
    //                },
                    type: "POST",
                    url: "/ajax/forfaits/updateRequestComments",
                    data: {request_id:request_id,comments:comments},
                    dataType:'json',
                    async: false,
                    success: function(response){
                        if(response === 'false'){
                            bootbox.alert({
                                message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El Comentario no ha sido guardado.<br/>Contacte con el administrador.</div>',
                                backdrop: true
                            });
                        }
                    },
                    error: function(response){
                        bootbox.alert({
                            message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El Comentario no ha sido guardado.<br/>Contacte con el administrador.</div>',
                            backdrop: true
                        });
                    }
                });
            }
        });
    });
    
    $('button.updateCommission').click(function(){
        button = $(this);
        request_id = button.attr('data-id');
        commission_td = button.parent('td').next('td').find('span.commission_value');
        
        bootbox.prompt({
            title: "Modificar Comission",
            value: "",
            callback: function(result) {

                if(result != null){
                    commission = result.replace(/\s/g,'');
                    
                    commission_td.text(commission);
                    request_id = button.attr('data-id');
                
                    $.ajax({
        //                headers: {
        //                    'X-CSRF-TOKEN': 
        //                },
                        type: "POST",
                        url: "/ajax/forfaits/updateCommissions",
                        data: {request_id:request_id,commission:commission},
                        dataType:'json',
                        async: false,
                        success: function(response){
                            if(response === 'false'){
                                bootbox.alert({
                                    message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. La Comisión no ha sido guardada.<br/>Contacte con el administrador.</div>',
                                    backdrop: true
                                });
                            }
                        },
                        error: function(response){
                            bootbox.alert({
                                message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. La Comisión no ha sido guardada.<br/>Contacte con el administrador.</div>',
                                backdrop: true
                            });
                        }
                    });
                }
            }
        });
    });
    
    $('a.launch_stripe').click(function(event){
        event.stopPropagation;
        stripe_div = $('div#stripe_div').clone().html();

        bootbox.alert({
            message: stripe_div,
            backdrop: true
        });
    });

</script>

@endsection

@section('scripts')

@endsection