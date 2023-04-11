@extends('layouts.admin-master')

@section('title') Porcentajes OTAs @endsection

@section('externalScripts') 
<style>
  .contentOtaPrices{
    padding: 2em 1em;
  }
  .contentOtaPrices .lst {
    clear: both;
    overflow: auto;
    content: "";
    padding: 0 7px;
  }
  .contentOtaPrices h6 {
    padding: 12px;
    margin-top: 1em;
    font-size: 16px;
    color: #FFF;
    background-color: #2a5d9b;
    font-weight: 800;
  }
  .contentOtaPrices .box {
    float: left;
    text-align: center;
    max-width: 33%;
    margin: 5px auto;
  }
  .contentOtaPrices span.ota {
    color: #e26d6d;
    font-weight: bold;
  }
  .contentOtaPrices span.admin {
    color: #2a5d9b;
    font-weight: bold;
  }
  .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus,
  .nav-tabs > li > a:hover, .nav-tabs > li > a:focus{
    background-color: #295d9b;
    border-color: #295d9b;
  }
  #myTab a{
    font-weight: 800;
  }
</style>
@endsection
@section('content')
<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3 col-xs-12">
          <h3>Precios de Temporadas:</h3>
        </div>
        <div class="col-xs-12 col-md-9">
          @include('backend.prices._navs')
        </div>
      </div>
    </div>
  </div>
  <div class="contentOtaPrices">
    <h1 class="text-center">Revisar Precios en Otas: </h1>


    <ul class="nav nav-tabs" id="myTab" role="tablist">
      @foreach($aAgenc as $k=>$name)
      <li class="nav-item">
        <a class="nav-link" id="{{$k}}-tab" data-toggle="tab" href="#ota{{$k}}" role="tab" aria-controls="{{$name}}" aria-selected="false">{{$name}}</a>
      </li>
      @endforeach
      <li class="nav-item">
        <a class="nav-link" id="ota_logs-tab" data-toggle="tab" href="#ota_logs" role="tab" aria-controls="ota_logs" aria-selected="false">Logs</a>
      </li>
    </ul>

    <div class="tab-content" id="myTabContent">


      <?php
      foreach ($lst as $plan => $channels) {
        ?>
        <div class="tab-pane fade" id="ota{{$plan}}" role="tabpanel" aria-labelledby="{{$plan}}-tab">
          <?php
          foreach ($channels as $ch => $lst) {
            ?>
            <h6><?php echo isset($aChRooms[$ch]) ? $aChRooms[$ch] : 'APARTAMENTO'; ?></h6>
            <div class="lst cleafix">
              <?php
              foreach ($lst as $i) {
                ?>
                <div class="box">
                  <div class="date">{{$i[0]}}</div>
                  <span class="admin">{{$i[1]}}</span> / 
                  <span class="ota ">{{$i[2]}}</span>
                </div>
                <?php
              }
              ?>
            </div>
            <?php
          }
          ?>
        </div>
        <?php
      }
      ?>
      <div class="tab-pane fade" id="ota_logs" role="tabpanel" aria-labelledby="ota_logs">
        <h6>Últimos 20 logs</h6>
        <?php echo $logLines; ?>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
      $('#myTab li:first-child a').tab('show');
  });
</script>
@endsection