@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 

    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />

    <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
    <link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
    <link href="/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" media="screen">

@endsection
    
@section('content')
<style>
  tbody>tr>td{
    padding: 10px!important;
    margin: 10px!important;
  }
</style>
<div class="container-fluid padding-25 sm-padding-10">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-left">
                <div class="col-xs-12 ">
                  <input type="text" id="search-table" class="form-control pull-right" placeholder="Buscar">
                </div>
              </div>

              <div class="clearfix"></div>

              <table class="table table-hover demo-table-search table-responsive table-striped" id="tableWithSearch">

                    <thead>
                        <th class ="text-center bg-complete text-white"> Reserva    </th>
                        <th class ="text-center bg-complete text-white"> Importe      </th>
                        <th class ="text-center bg-complete text-white"> Tipo    </th>
                        <th class ="text-center bg-complete text-white"> Comentario  </th>
                    </thead>
                    <tbody>
                        <?php if (count($pagos) >0): ?>
                            
                            <?php foreach ($pagos as $pago): ?>
                                
                                <tr>
                                    <td class="text-center"><a class="update-book" data-id="<?php echo $pago->book_id ?>"  title="Editar Reserva"  href="{{url ('reservas/update')}}/<?php echo $pago->book_id ?>"><?php echo $pago->book->customer->name ?></a></td>
                                    <td class="text-center"><?php echo $pago->import ?></td>                                    
                                    <td class="text-center"><?php echo $book->getTypeCobro($pago->type) ?></td>
                                    <td class="text-center"><?php echo $pago->comment ?></td>
                                </tr> 

                            <?php endforeach ?>
                            
                        <?php endif ?>
                    </tbody>

                </table>

        </div>
    </div>
</div>

@endsection

@section('scripts')

   <script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
   <script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
   <script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
   <script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
   <script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
   <script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>

   <script src="/assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
   <script type="text/javascript" src="/assets/plugins/jquery-autonumeric/autoNumeric.js"></script>
   <script type="text/javascript" src="/assets/plugins/dropzone/dropzone.min.js"></script>
   <script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
   <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
   <script src="/assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
   <script src="/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
   <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
   <script src="/assets/plugins/summernote/js/summernote.min.js" type="text/javascript"></script>
   <script src="/assets/plugins/moment/moment.min.js"></script>
   <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
   <script src="/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
   <script src="/assets/plugins/bootstrap-typehead/typeahead.bundle.min.js"></script>
   <script src="/assets/plugins/bootstrap-typehead/typeahead.jquery.min.js"></script>
   <script src="/assets/plugins/handlebars/handlebars-v4.0.5.js"></script>



@endsection