<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Contabilidad  @endsection

@section('externalScripts') 
<style type="text/css">
  .after-banks{
    min-height: 60em;
  }
</style>
@endsection

@section('content')
<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-12">
        <h2 class="text-center">
          BANCO 
        </h2>
      </div>

    </div>
  </div>
  <div class="row mb-1em">
         @include('backend.sales._button-contabiliad')
        </div>
</div>
<div class="container-fluid">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12 contentBank">

      <iframe 
        src="https://www.afterbanks.com/appmain/static/?startAt=globalPosition#" 
        width="100%"
        class="after-banks"
        ></iframe>


    </div>
  </div>
</div>

@endsection	
