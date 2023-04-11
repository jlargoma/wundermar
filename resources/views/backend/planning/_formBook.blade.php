<?php   
    use \Carbon\Carbon;  
    setlocale(LC_TIME, "ES"); 
    setlocale(LC_TIME, "es_ES"); 
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts')
    <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
    <link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
    <style type="text/css" media="screen"> 
        .daterangepicker{
            z-index: 10000!important;
        }
        .pg-close{
            font-size: 45px!important;
            color: white!important;
        }
        @media only screen and (max-width: 767px){
           .daterangepicker {
                left: 12%!important;
                top: 3%!important; 
            }
        }

    </style>
@endsection
    
@section('content') 
    <div class="container">
        <div class="col-md-12 m-t-10">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close " data-dismiss="alert" aria-hidden="true" style="right: 0; ">×</button>
                <h3 class="font-w300 push-15">Error</h3>
                <p class="font-s18">Este apartamento 
                    <a class="alert-link" href="javascript:void(0)"><u>ya tiene una reserva confirmada</u>  Puedes cambiar los datos aquí mismo</a>!
                </p>
            </div>
        </div>
        <div class="row">
            @include('backend.planning._nueva')
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
@include('backend.planning._bookScripts', ['update' => 0])
@endsection