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

<div class="container-fluid padding-25 sm-padding-10">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Temporadas</h2>
        </div>
        <div class="col-md-5">
                <table class="table table-hover  table-condensed table-striped" >
                    <thead>
                        <tr>
                            <th class ="text-center hidden">    id      </th>
                            <th class ="text-center bg-complete text-white"> Nombre    </th> 
                            <th class ="text-center bg-complete text-white"> Inicio  </th>
                            <th class ="text-center bg-complete text-white"> Fin     </th>
                                             
                            <th class ="text-center bg-complete text-white"> Eliminar  </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seasons as $season): ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $season->typeSeasons->name ?>
                                </td>  
                                <td class="text-center" hidden><?php echo $season->id ?></td>
                                <td class="text-center">
                                    <?php  echo date('d-M-Y',strtotime($season->start_date))?>
                                </td>
                                <td class="text-center">
                                    <?php echo date('d-M-Y',strtotime($season->finish_date)) ?>
                                </td>
                                            
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ url('/admin/temporadas/delete/')}}/<?php echo $season->id ?>" class="btn btn-tag btn-danger" type="button" data-toggle="tooltip" title="" data-original-title="Eliminar Temporada" onclick="return confirm('¿Quieres eliminar la temporada?');">
                                            <i class="fa fa-trash"></i>
                                        </a>                                     
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
        </div>
        <div class="col-md-7">
            @include('backend.seasons.calendar')
            <div class="row">

                <div class="col-md-4">
                    <div class="sm-m-l-5 sm-m-r-5">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                            Tipo de temporada
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title col-md-6">Agregar Tipo Temporada
                                        </div>
                                        <div class="panel-title col-md-6">Temporada
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-md-6">
                                            <form role="form"  action="{{ url('/admin/temporadas/create-type') }}" method="post">
                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                <div class="input-group transparent">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                                    <input type="text" class="form-control" name="name" placeholder="nombre" required="" aria-required="true" aria-invalid="false">
                                                </div>
                                                    <br>
                                                <div class="input-group">
                                                    <button class="btn btn-complete" type="submit">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                        <?php foreach ($newtypeSeasons as $newtypeSeason): ?>
                                            <div>
                                                <?php echo $newtypeSeason->name ?>
                                            </div>
                                        <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sm-m-l-5 sm-m-r-5">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Temporada
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="panel-title col-md-6">Agregar Temporada
                                                </div>
                                                <div class="panel-title col-md-6">Temporada
                                                </div>
                                            </div>
                                            <form role="form"  action="{{ url('/admin/temporadas/create') }}" method="post">
                                                <div class="panel-body">
                                                    <div class="col-md-12">
                                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                        <div class="input-group transparent">
                                                            <span class="input-group-addon">
                                                                <i class="pg-plus_circle"></i>
                                                            </span>
                                                            <select class="full-width" data-init-plugin="select2" name="type">
                                                                <?php foreach ($typeSeasons as $typeSeason): ?>
                                                                     <option value="<?php echo $typeSeason->id ?>"><?php echo $typeSeason->name ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                        <br>
                                                        <div class="input-daterange input-group" id="datepicker-range">

                                                            <input id="start" type="text" class="input-sm form-control" name="start" data-date-format="dd-mm-yyyy">
                                                            <span class="input-group-addon">Hasta</span>
                                                            <input id="finish" type="text" class="input-sm form-control" name="finish" data-date-format="dd-mm-yyyy">
                                                        </div>
                                                        <br>
                                                        <div class="input-group">
                                                            <button class="btn btn-complete" type="submit">Guardar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

   
    <script type="text/javascript">
        $(document).ready(function() {

            $('.new-seasons').click(function(event) {
                $.get('/admin/temporadas/new', function(data) {
                    $('#content-seasons').empty().append(data);
                });
            });
            $('.new-type-seasons').click(function(event) {
                $.get('/admin/temporadas/new-type', function(data) {
                    $('#content-seasons').empty().append(data);
                });
            });

            $('.editables').change(function(event) {
                var id = $(this).attr('data-id');

                var name = $('.name-room-'+id).val();

                $.get('/admin/apartamentos/update/', {  id: id, name:name}, function(data) {
                    alert(data);
                });
            });
        });
    </script>
@endsection