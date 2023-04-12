@extends('layouts.admin-master')

@section('title') Precios de apartamentos @endsection

@section('externalScripts') 

<link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
      integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3 col-xs-12">
          <h3>Promociones de Temporada:</h3>
        </div>
        <div class="col-xs-12 col-md-5">
          <a class="text-white btn btn-md btn-primary" href="{{route('precios.base')}}">PRECIO BASE X TEMP</a>
          <a class="text-white btn btn-md btn-primary" href="{{route('channel.price.cal')}}">UNITARIA</a>
          <a class="text-white btn btn-md btn-primary" href="{{route('channel.price.site')}}">EDIFICIO</a>
          
          <button class="btn btn-md btn-primary active"  disabled>PROMOCIONES</button>

        </div>
        <div class="col-md-4 row">
          <div class="col-md-6">@include('backend.years._selector', ['minimal' => true])</div>
        </div>
      </div>
    </div>
  </div>

  <div class="tab-content tb-promotions">
@include('backend.prices.blocks._filters')
    <div class="col-md-12 table-responsive">
      <table class="table promotions table-hover">
        <thead>
          <tr>
            <th>Channel</th>
            <th class="text-center">Sitio</th>
            <th >Apto</th>
            <th class="text-center">Descuento</th>
            <th>Detalle</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @if($oPromotions)
          @foreach($oPromotions as $prom)
          <tr>
            <td>{{$prom->channelName}}</td>
            <td class="text-center">{{$prom->siteName}}</td>
            <td>{!! implode('<br/>',$prom->room_group) !!}</td>
            <td class="text-center">{{$prom->discount}}</td>
            <td>{{$prom->detail}}</td>
            <td><i class="fa fa-trash"></i></td>
          </tr>
          @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>

<style>
  .filter-field {
    width: 18em;
    display: inline-block;
    margin-right: 1em;
}
  .tabRooms {
    background-color: #004a2f;
    display: inline-block;
    padding: 7px 14px;
    border-radius: 2px;
    margin: 6px 0;
    color: #fff;
    cursor: pointer;
    float: left;
    margin-right: 5px;
    margin-bottom: 1em;
}
 span.tabRooms.active {
    color: #004a2f;
    background-color: #FFF;
    border: 1px solid #004a2f;
}
 .tb-promotions th {
    background-color: #1f7b00;
    color: #FFFFFF !important;
}
.tb-promotions .table tbody tr td {
    padding: 8px 8px !important;
}
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:after,
table.dataTable thead .sorting_asc_disabled:after,
table.dataTable thead .sorting_desc_disabled:after{
  display: none !important;
}
</style>
<script type="text/javascript">
  $(document).ready(function () {

        var dataTable = $('.promotions').DataTable({
           order: [[ 3, "asc" ]],
           pageLength: 30,
           pagingType: "full_numbers",
           @if($isMobile)
             scrollX: true,
             scrollY: false,
             scrollCollapse: true,
             fixedColumns:   {
               leftColumns: 1
             },
           @endif

         });
      
       var sendFormPromotion = function(){
      $('#prom_filters').submit();
    }
    $('#site').on('change',function (event) {
      sendFormPromotion();
    });
    $('#ch_sel').on('change',function (event) {
      sendFormPromotion();
    });
    $('.tabRooms').on('click',function (event) {
      
      $('#room_gr_sel').val($(this).data('k'));
       sendFormPromotion();
    });
    
    
  });
</script>
@endsection