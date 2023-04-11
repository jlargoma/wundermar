@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 

    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 bg-white">
  @include('backend.years.selector', ['minimal' => false])
    <div class="container clearfix">
        <div class="col-md-6 col-xs-12 text-left push-30">
            <h2 class="font-w300" style="margin: 0">LISTADO DE <span class="font-w800">CLIENTES</span></h2>
        </div>

        <div class="col-md-2 col-xs-12 text-center pull-right push-30">
            <a class="btn btn-success btn-cons" href="{{ url('/admin/customers/importExcelData') }}">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> <span class="bold">Exportar excel</span>
            </a>
        </div>

        <div class="col-xs-12">
            <div class="pull-left push-20">
              <div class="col-xs-12 not-padding">
                <input type="text" id="search-table" class="form-control" placeholder="Buscar">
              </div>
            </div>
            <div class="col-xs-12" id="containerTableResult">
                @include('backend.customers._table')
            </div>
            
        </div>
    </div>
</div>




@endsection

@section('scripts')
    <script src="/assets/js/notifications.js" type="text/javascript"></script>
    <script src="/assets/js/scripts.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.update-customer').click(function(event) {
                var id = $(this).attr('data-id');
                $.get('/admin/clientes/update/'+id, function(data) {
                    $('.modal-body').empty().append(data);
                });
            });

            $('.deleteCustomer').click(function(event) {
                var id = $(this).attr('data-id');
                var line = "#customer-"+id;
                if (confirm('Desea borrar los datos del cliente en forma permanente?')){
                $.get('/admin/customer/delete/'+id, function(data) {
                    if (data == "ok") {
                        $(line).hide();
                        alert('Datos del cliente borrados');
                    } else {
                        alert(data);
                    }
                  });
                }
            });

            $('#search-table').keypress(function(event) {
                var searchString = $(this).val();

                $.get('/admin/customers/searchByName/'+searchString, function(data) {

                    $('#containerTableResult').empty().append(data);
                      
                    
                });

            });
        });
    </script>
@endsection