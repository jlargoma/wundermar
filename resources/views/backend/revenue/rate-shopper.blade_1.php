@extends('layouts.admin-master')

@section('title') Revenue @endsection

@section('externalScripts')

<style>
  @media only screen and (max-width: 768px){
  }

  .contenedor .table tr th,
  .contenedor .table tr td{
    text-align: center;
  }
  .contenedor .table tr th.static,
  .contenedor .table tr td.static{
    text-align: left;
  }
  
  .contenedor{
    max-width: 92%;
    margin: 1em auto;
  }
  .contenedor .static-empty {
    background-color: #fafafa !important;
    width: 110px;
  }

  tr.competitor {
    position: relative;
    height: 47px;
}

.competitor td {
    background-color: #6d5cae !important;
    color: #FFF;
    font-size: 1.5em !important;
    text-align: left !important;
    padding-left: 3em !important;
    position: absolute;
    z-index: 19;
    width: 56em;
        padding-top: 5px !important;
}
td.static {
    width: 250px !important;
}
.rate-shopper .table-resumen .first-col {
    padding-left: 250px !important;
}
</style>
@endsection

@section('content')

<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-6 text-right">
        <h2 class="text-center">
          Revenue
        </h2>
      </div>
      <div class="col-md-2 col-xs-4 sm-padding-10" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  </div>
  <div class="row mb-1em text-center">
    @include('backend.revenue._buttons')
  </div>
  
  <div class="contenedor rate-shopper">
    <div class="table-responsive ">
      <table class="table table-resumen">
        <thead>
          <tr class="resume-head">
            <th class="static">Competidor</th>
            <th class="first-col"></th>
            @foreach($range as $k=>$v)
            <th>{{$v}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($competitors as $kComp=>$comp)
            <tr class="competitor">
              <td colspan="20">{{$comp['name']}}</td>
            </tr>
            @if($comp['snaphot'])
              @foreach($comp['snaphot']['lstRooms'] as $hab)
              <tr>
                <td class="static">{{$hab['name']}}</td>
                <td class="nowrap first-col"></td>
                @foreach($range as $k=>$v)
                <td class="nowrap">
                  <?php 
                  if (isset($hab['prices'][$k]) && $hab['prices'][$k]>0):
                    echo $hab['prices'][$k].'€';
                  else:
                    echo '--';
                  endif;
                  ?>
                </td>
                @endforeach
              </tr>
              @endforeach
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {


  });
</script>
@endsection