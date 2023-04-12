@extends('layouts.admin-master')

@section('title') Administrador de reservas  @endsection

@section('externalScripts')
    <link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css"
          media="screen"/>

@endsection

@section('content')
    <div class="container padding-25 sm-padding-10">
        <div class="row">
            <div class="col-md-12 col-xs-12 text-center">
                <h2 class="text-center font-w300" style="font-weight: 300; letter-spacing: -2px;">Administracion de
                    <span
                            class="font-w800" style="font-weight: 800;">Usuarios</span></h2>
            </div>
            <div class="col-md-12">
                <div class="col-md-9 col-xs-12">
                    <div class="col-md-3 col-xs-12">
                        <input type="text" class="form-control" placeholder="Bucar..." id="searchUser"/>
                        <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <button class="btn btn-primary" style="float:right; margin: 0 5px" type="button" data-toggle="modal"
                            data-target="#newUser">
                        <i class="fa fa-plus"></i> Usuario
                    </button>
                </div>
            </div>

            <div class="col-md-12 table-responsive" id="content-table-user">

                @include('backend.users._tableUser')

            </div>

        </div>
    </div>

    <div class="modal fade slide-up disable-scroll in" id="newUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content-wrapper">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                class="fa fa-close fs-50"></i>
                    </button>
                    <div class="container-xs-height full-height">
                        <div class="row-xs-height">
                            <div class="modal-body col-xs-height col-middle text-center   ">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                       aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                        Agregar usuario
                                    </a>
                                </h4>
                                <div class="col-xs-12">
                                    <form role="form" action="{{ url('/admin/usuarios/create') }}" method="post">
                                        <input type="hidden" name="_token"value="<?php echo csrf_token(); ?>">
                                        <div class="input-group transparent">
                                            <span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" name="name"
                                                   placeholder="Nombre" required="" aria-required="true"
                                                   aria-invalid="false">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="pg-phone"></i>
                                            </span>
                                            <input type="number" class="form-control" name="phone"
                                                   placeholder="Telefono" required="" aria-required="true"
                                                   aria-invalid="false">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="pg-plus_circle"></i>
                                            </span>
                                            <select class="full-width form-control"  name="role" required placeholder="Seleccione role">
                                                <option></option>
                                                <option value="admin">admin</option>
                                                <option value="subadmin">SubAdmin</option>
                                                <option value="limpieza">Limpieza</option>
                                                <option value="agente">Agente</option>
                                                <option value="propietario">Propietario</option>
                                                <option value="recepcionista">Recepcionista</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-key"></i>
                                            </span>
                                            <input type="password" class="form-control" name="password"
                                                   required="" aria-required="true" aria-invalid="false">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="pg-mail"></i>
                                            </span>
                                            <input type="email" class="form-control" name="email"
                                                   placeholder="Email" required="" aria-required="true"
                                                   aria-invalid="false">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <button class="btn btn-complete" type="submit">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade slide-up disable-scroll in" id="updateUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content-wrapper">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                class="fa fa-close fs-50"></i>
                    </button>
                    <div class="container-xs-height full-height">
                        <div class="row-xs-height update-content">

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">
      $(document).ready(function () {
        $('.update-user').click(function (event) {
          var id = $(this).attr('data-id');
          $.get('/admin/usuarios/update/' + id, function (data) {
            $('.update-content').empty().append(data);
          });
        });

        $('.editables').change(function (event) {
          var id = $(this).attr('data-id');

          var name = $('.name-user-' + id).val();
          var email = $('.email-user-' + id).val();

          $.get('/admin/usuarios/ajax', {id: id, name: name, email: email}, function (data) {
            alert(data);
          });
        });

        $('#searchUser').keydown(function (e) {
          var search = $(this).val();
          var token = $('#_token').val();

          $.post('/admin/usuarios/search', {search: search, _token: token}).done(function
              (data) {
            $('#content-table-user').empty().append(data);
          });

        });
      });
    </script>
@endsection