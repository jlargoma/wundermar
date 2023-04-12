<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas  @endsection

@section('externalScripts') 
<link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
  td{
    padding: 8px!important;
  }
  #tableInvoices .fa.order{
    font-size: 11px;
        color: #FFF;
  }
  #tableInvoices thead th{
        color: #FFF;
    text-align: center;
    background-color: #1f7b00;
  }
  #tableInvoices thead th::after{
    content: none !important;
    display: none !important;
  }
</style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 bg-white">
  <div class="container clearfix">
    <div class="row">

      <div class="col-md-3  col-xs-12  push-20">
        <div class="col-xs-12">
          <a class="text-white btn btn-md btn-success" href="{{ route('invoice.edit',-1) }}"><i class="fa fa-plus"></i> Factura en blanco</a>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xs-12 text-left">
     
      <h2 class="font-w300" style="margin: 0">LISTADO DE <b>FACTURAs ({{moneda($totalValue)}}) </b></h2>
    </div>
    <div class="col-md-3 pull-right col-xs-12 text-left">
      <a href="{{ url('admin/facturas/descargar-todas') }}" class="text-white btn btn-md btn-primary" target="_black">
        Descargar Todas
      </a>
    </div>
    <div class="col-xs-12 bg-white">
      <div class="row">
        <div class="col-md-12 col-xs-12" id="table-customers" >
          @include('backend.invoices._table')
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>

<script type="text/javascript">

  $(document).ready(function() {
    
    $('#search-table').keyup(function (event) {
      var searchString = $(this).val();
      $.get('/admin/invoices/searchByName/' + searchString, function (data) {
        $('#table-customers').empty();
        $('#table-customers').append(data);
      });
    });

    $('.dni-customer').change(function () {

      var idCustomer = $(this).attr('idCustomer');
      var dni = $(this).val();
      $.get("/admin/cliente/dni/" + idCustomer + "/update/" + dni, function (data) {
        console.log(data);
      });

    });
  
    var dataTable = $('.table-data').DataTable({
          "paging":   false,
          "order": [[ 0, "asc" ]],
          paging:  true,
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
         });
</script>
@endsection