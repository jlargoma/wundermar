<?php   
use \Carbon\Carbon;
use \App\Classes\Mobile;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$mobile = new Mobile();
$isMobile = $mobile->isMobile();
?>
@extends('layouts.admin-master')

@section('title') Contabilidad  @endsection

@section('externalScripts')
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
  <style>
    tr.text-center.contab-site,
    tr.text-center.contab-site td{
      color: #fff;
      font-weight: 600;
      background-color: #6d5cae;
    }
    tr.text-center.contab-ch {
        color: blue;
        font-weight: 600;
    }

    i.fas.fa-plus-circle.toggle-contab-site,
    i.fas.fa-plus-circle.toggle-contab-extra,
    i.fas.fa-plus-circle.toggle-contab {
      padding: 5px;
      cursor: pointer;
    }
    
    .contab-ch.tr-close,
    .contab-extras.tr-close,
    .contab-room.tr-close{
      display: none;
    }
    span.ocup {
        color: red;
        font-size: 11px;
        display: block;
    }
    .pieChart {
      max-width: 120px;
      margin: 0 auto;
    }
    
    .circle-percent {
      position: relative;
      max-width: 200px;
      margin: 4em auto 0;
    }
    .circle-percent span {
        position: absolute;
        top: 53%;
        left: 36%;
        font-size: 1.5em;
    }
    .circle-percent canvas {
          margin-top: -90px;
    }
    .table-responsive>.table>tbody#tableItems>tr>td{
       white-space: normal;
      border-left: solid 1px #cacaca;
      padding: 8px !important;
    }
    .table-responsive>.table>tbody#tableItems>tr.selected {
      color: #000;
    }
    
    .table-contab .static{
      white-space: nowrap; width: 130px;color: black;
      overflow-x: scroll;
      margin-top: 1px;
      padding: 5px 9px !important;
    }
    @media only screen and (max-width: 425px){
      h5{
        font-size: 13px !important;
      }
      .circle-percent span{
        font-size: 13px !important;
      }
    }
  </style>
@endsection

@section('content')
    <div class="box-btn-contabilidad">
      <div class="row show-mobile">
        <div class="col-xs-8">
          <h2>Contabilidad</h2>
        </div>
        <div class="col-xs-4">
          @if($isMobile)  @include('backend.years._selector', ['minimal' => true]) @endif
        </div>
      </div>
      <div class="row bg-white hidden-mobile">
        <div class="col-md-12 col-xs-12">

          <div class="col-md-3 col-md-offset-3 col-xs-12">
            <h2 class="text-center">
              Contabilidad
            </h2>
          </div>
          <div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
            @if(!$isMobile)  @include('backend.years._selector')@endif
          </div>
        </div>
      </div>
      <div class="row mb-1em">
        @include('backend.sales._button-contabiliad')
      </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-xs-12">
                <div class="col-lg-6 col-md-6 hidden-mobile">
                    <div>
                        <canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6  hidden-mobile">
                    <div>
                        <canvas id="barChart2" style="width: 100%; height: 250px;"></canvas>
                    </div>
                </div>
              <div class="col-xs-12">
                @include('backend.sales._by_season')
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-xs-12">
                @include('backend.sales._stats')
            </div>
            <div class="col-lg-4 col-md-6 col-xs-12">
                <div class="col-md-12 col-xs-12">
                    <?php $dataChartYear = \App\Rooms::getPvpByMonth(($year->year - 1 )) ?>
                    <?php $dataChartPrevYear = \App\Rooms::getPvpByMonth(($year->year - 2 )) ?>
                  
                    <canvas id="chartTotalByMonth" style="width: 100%; height: 250px;"></canvas>
                  
                </div>
                <div class="col-md-12 col-xs-12">
                  <canvas id="barChartMonth" style="width: 100%; height: 250px;"></canvas>
                </div>
            </div>

        </div>
        <div class="row bg-white">
            <div class="col-lg-8 col-md-8 col-xs-12">
                <div class="row table-responsive" style="border: 0px!important">
                    <table class="table table-contab">
                       <thead>
                        <tr>
                          @if($isMobile)
                          <th class="text-center bg-complete text-white static" >
                            <th class="text-center bg-complete text-white first-col" style="padding-left: 145px !important;padding-right: 11px !important;">total<br/>
                          @else
                            <th class="text-center bg-complete text-white" >Apto</th>
                            <th class="text-center bg-complete text-white" >total<br/>
                          @endif
                              
                              <?php echo number_format( $t_all_rooms, 0, ',', '.' ); ?>€
                            </th>
                            <th class="text-center bg-complete text-white">%</th>
                            @foreach($lstMonths as $k => $month)
                            <th class="text-center bg-complete text-white">
                              {{getMonthsSpanish($month['m'])}}<br/>
                              <?php
                              if (isset($t_room_month[$month['m']]) && $t_room_month[$month['m']]>1){
                                echo number_format( $t_room_month[$month['m']], 0, ',', '.' ).'€';
                              } else {
                                echo '--';
                              }
                              ?>
                            </th>
                            @endforeach
                            
                       <tbody>
                         
                            
                       
        <tr class="text-center">
          @if($isMobile)
          <td class="text-left static" >
              <i class="fas fa-plus-circle toggle-contab-extra"></i> Extras</th>
            <th class="text-center first-col">  
          @else
            <th class="text-left" style="width: 130px;"> <i class="fas fa-plus-circle toggle-contab-extra"></i>Extras</th>
            <th class="text-center ">  
          @endif
            {{moneda($months_extras[0])}}
            </th>
            <th class="text-center">&nbsp;</th>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">
              <?php echo isset($months_extras[$month['m']]) ? moneda($months_extras[$month['m']],false) : 0; ?>
            </th>
            @endforeach
        </tr>
         @foreach($extrasList as $extra_id => $item)
          <tr class="text-center contab-extras tr-close">
          @if($isMobile)
          <td class="text-left static" >
                {{$extraTit[$extra_id]}}
              </td>
              <th class="text-center first-col" style="padding-right:13px !important;padding-left: 135px!important">  
          @else
              <td class="text-left" style="width: 130px;padding-left: 5px!important">  
                {{$extraTit[$extra_id]}}
              </td>
              <th class="text-center ">  
          @endif
              {{moneda($item[0])}}
              </th>
              <td class="text-center">
                <?php 
                $percent = ($months_extras[0]> 0) ? ($item[0] / $months_extras[0]) * 100 : 0; 
                echo round($percent).'%';
                ?>
              </td>
            @foreach($lstMonths as $k => $month)
              <th class="text-center">{{moneda( $item[$month['m']],false)}}</th>
            @endforeach
          </tr>
          @endforeach

                        
                        
                        
                        @foreach($siteRooms as $site => $data)
                          <tr class="text-center contab-site" data-id="{{$site}}">
                          @if($isMobile)
                              <td class="text-left static">
                                <i class="fas fa-plus-circle toggle-contab-site" data-id="{{$site}}"></i>{{$data['t']}}
                              </td>
                              <th class="text-center first-col" style="padding-right:13px !important;padding-left: 135px!important">  
                          @else
                              <td class="text-left" style="padding-left: 11px !important;">  
                                <i class="fas fa-plus-circle toggle-contab-site" data-id="{{$site}}"></i>{{$data['t']}}
                              </td>
                              <th class="text-center ">  
                          @endif
                              {{moneda($data['months'][0])}}
                              </th>
                              <td class="text-center">
                                <?php 
                                $percent = ($data['months'][0] * 100 / $t_all_rooms); 
                                ?>
                                {{round($percent)}}%
                              </td>
                          @foreach($lstMonths as $k => $month)
                            <th class="text-center">
                              <?php
                              $k_month = $month['m'];
                              if (isset($data['months'][$k_month]) && $data['months'][$k_month]>1){
                                echo moneda($data['months'][$k_month]);
                              } else {
                                echo '--';
                              }
                              ?>
                            </th>
                          @endforeach
                          </tr>
                          
 <!-- BEGIN: channels-->                               
                          @foreach($data as $ch => $data2)
                          <?php if ($ch == 't' || $ch == 'months' || $ch == 'channels' || trim($ch) ==''){ continue;} ?>
                          <tr class="text-center contab-ch contab-ch-{{$site}} tr-close">
                          @if($isMobile)
                          <td class="text-left static" >
                                <i class="fas fa-plus-circle toggle-contab" data-id="{{$ch}}"></i>{{$channels[$ch]}}
                                <span class="ocup">% Ocup.</span>
                              </td>
                              <th class="text-center first-col" style="padding-right:13px !important;padding-left: 135px!important">  
                          @else
                              <td class="text-left" style="width: 130px;">  
                                <i class="fas fa-plus-circle toggle-contab" data-id="{{$ch}}"></i>{{$channels[$ch]}}
                                <span class="ocup">% Ocup.</span>
                              </td>
                              <th class="text-center ">  
                          @endif
                              {{moneda($data2['months'][0])}}
                              </th>
                              <td class="text-center">
                                <?php 
                                $percent = 0;
                                if ($data2['months']>1)
                                  $percent = ($data2['months'][0] * 100 / $t_all_rooms); 
//                                  $percent = ($data2['months'][0] * 100 / $data['months'][0]); 
                                ?>
                                {{round($percent)}}%
                              </td>
                          @foreach($lstMonths as $k => $month)
                            <th class="text-center">
                              <?php
                              $k_month = $month['m'];
                              if (isset($data2['months'][$k_month]) && $data2['months'][$k_month]>1){
                                echo moneda($data2['months'][$k_month]);
                              } else {
                                echo '--';
                              }
//                              dd($ch_monthOcupPercent);
                              ?>
                              @if(isset($ch_monthOcupPercent[$ch][$k_month]) && $ch_monthOcupPercent[$ch][$k_month]>0)
                              <span class="ocup">{{$ch_monthOcupPercent[$ch][$k_month]}} %</span>
                              @endif
                            </th>
                          @endforeach
                          </tr>
                            <!-- BEGIN: ROOMS-->   
                           @foreach($data2['rooms'] as $roomID => $name)
                          <tr class="text-center contab-room contab-room-{{$ch}} contab-rsite-{{$site}}  tr-close">
                          @if($isMobile)
                              <td class="text-left static">
                                {!!$name!!}
                              </td>
                              <th class="text-center first-col" style="padding-right:13px !important;padding-left: 135px!important">  
                          @else
                              <td class="text-left" style="width: 130px;padding-left: 5px!important">  
                                {!!$name!!}
                              </td>
                              <th class="text-center ">  
                          @endif
                               <?php
                                $totalRoom = 0;
                                if (isset($t_rooms[$roomID]) && $t_rooms[$roomID]>1){
                                  $totalRoom = $t_rooms[$roomID];
                                  echo moneda($totalRoom);
                                } else {
                                  echo '--';
                                }
                                ?>
                              </th>
                              <td class="text-center">
                                <?php 
                                $percent = ($totalRoom / $t_all_rooms) * 100; 
                                echo round($percent).'%';
                                ?>
                              </td>
                          @foreach($lstMonths as $k => $month)
                            <th class="text-center">
                              <?php
                              $k_month = $month['m'];
                              if (isset($sales_rooms[$roomID]) && isset($sales_rooms[$roomID][$k_month]) && $sales_rooms[$roomID][$k_month]>1){
                                echo moneda( $sales_rooms[$roomID][$k_month]);
                              } else {
                                echo '--';
                              }
                              ?>
                            </th>
                          @endforeach
                          </tr>
                          @endforeach
                          
  <!-- END: ROOMS-->                      
                          @endforeach
  <!-- END: channels-->                      
                        @endforeach
                        
                       </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
     
              <div class="row">
                <div class="col-xs-4">
                  <div class="box">
                    <div class="sub-title">Ocupación Habitaciones</div>
                    <h5>RIAD</h5>
                    <?php 
                    $pieOcuRiad = 0;
                    if ($dispBySite[1]['c']) $pieOcuRiad = round($dispBySite[1]['t']/$dispBySite[1]['c']);
                    ?>
                    <div class="circle-percent">
                      <canvas id="pieOcuRiad" style="width: 100%; height: 170px;"></canvas>
                      <span>{{$pieOcuRiad}} %</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-xs-4">
                  <div class="box">
                    <div class="sub-title">Ocupación Habitaciones</div>
                    <h5>Gloria</h5>
                    <?php 
                    $pieOcuGloria = 0;
                    if ($dispBySite[3]['c']) $pieOcuGloria = round($dispBySite[3]['t']/$dispBySite[3]['c']);
                    ?>
                    <div class="circle-percent">
                      <canvas id="pieOcuGloria" style="width: 100%; height: 170px;"></canvas>
                      <span>{{$pieOcuGloria}} %</span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-4">
                  <div class="box">
                    <div class="sub-title">Ocupación Habitaciones</div>
                    <h5>SILOE</h5>
                    <?php 
                    $pieOcuSiloe = 0;
                    if ($dispBySite[5]['c']) $pieOcuSiloe = round($dispBySite[5]['t']/$dispBySite[5]['c']);
                    ?>
                    <div class="circle-percent">
                      <canvas id="pieOcuSiloe" style="width: 100%; height: 170px;"></canvas>
                      <span>{{$pieOcuSiloe}} %</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-4">
                <div class="box">
                  <div class="sub-title">Ocupación Habitaciones</div>
                  <h5>Zahira </h5>
                  <?php 
                  $pieOcuZahira = 0;
                  if ($dispBySite[6]['c']) $pieOcuZahira = round($dispBySite[6]['t']/$dispBySite[6]['c']);
                  ?>
                  <div class="circle-percent">
                    <canvas id="pieOcuZahira" style="width: 100%; height: 170px;"></canvas>
                    <span>{{$pieOcuZahira}} %</span>
                  </div>
                </div>
              </div>
             
              <div class="col-md-12 col-xs-12">
                  <h3>Resumen Extras PVP</h3>
                  <div class=" table-responsive">
                  <table class="table table-resumen">
                    <thead>
                      <tr class="resume-head">
                        <th class="static">Concepto</th>
                        <th class="first-col">Total</th>
                        @foreach($lstMonths as $k => $month)
                        <th>{{getMonthsSpanish($month['m'])}}</th>
                        @endforeach
                      </tr>
                   </thead>
                   <tbody>
                     
                      @foreach($extrasGroup as $k=>$item)
                      <tr>
                        <td class="static">{{$extTyp[$k]}}</td>
                        <?php $auxClass = ' class="first-col" '; ?>
                        @foreach($item as $month=>$val)
                        <td {{$auxClass}} >{{moneda($val)}}</td>
                        <?php $auxClass = ''; ?>
                        @endforeach
                      </tr>
                       @endforeach
                      
                     </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection

<!---->
@section('scripts')
    <script type="text/javascript">

      <?php $totalYearSite = \App\Rooms::getPvpLastYears_site($year->year); ?>
      var data = {
        labels: [

	  <?php 
            $lastThreeSeason = Carbon::createFromFormat('Y', $year->year)->subYears(3);
	    for ($i=1; $i <= 4; $i++): 
              echo "'" . $lastThreeSeason->format('Y') ."',";
              $lastThreeSeason->addYear();
            endfor; 
          ?>
        ],
        datasets: [
          {
            label: "RIAD",
            borderColor: '#6d5cae',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[1]}}],
          },
          {
            label: "Gloria",
            borderColor: 'green',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[3]}}],
          },

          {
            label: "Siloé",
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[5]}}],
          },
          {
            label: "Zahira",
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[6]}}],
          }
        ]
      };
       var myBarChart = new Chart('barChart', {
        type: 'line',
        data: data,
      });
                 
      var data = {
        labels: [

	        <?php $lastThreeSeason = Carbon::createFromFormat('Y', $year->year)->subYears(3) ?>
	        <?php for ($i=1; $i <= 4; $i++): ?>
	            <?php echo "'" . $lastThreeSeason->format('Y') ."'," ?>
                <?php $lastThreeSeason->addYear(); ?>
            <?php endfor; ?>
        ],
        datasets: [
          {
            label: "Ingresos por Temp",
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            data: [
	            <?php $lastThreeSeason = Carbon::createFromFormat('Y', $year->year)->subYears(3) ?>
                <?php for ($i=1; $i <= 4; $i++): ?>
                    <?php $totalYear = \App\Rooms::getPvpByYear($lastThreeSeason->copy()->format('Y')); ?>
                    <?php echo "'" . $totalYear. "'," ?>
                    <?php $lastThreeSeason->addYear(); ?>
                <?php endfor; ?>
            ],
          }
        ]
      };

        

      var myBarChart = new Chart('barChart2', {
        type: 'bar',
        data: data,
      });


      


      new Chart(document.getElementById("chartTotalByMonth"), {
        type: 'line',
        data: {
          labels: [
            <?php foreach ($dataChartMonths as $key => $value) echo "'" . $key . "',";?>
          ],
          datasets: [{
            data: [
              <?php foreach ($dataChartMonths as $key => $value) echo "'" . round($value) . "'," ?>
            ],
            label: '<?php echo $year->year ?>',
            borderColor: "rgba(54, 162, 235, 1)",
            fill: false
            },
            {
              data: [
		<?php foreach ($dataChartYear as $key => $value) echo "'" . round($value) . "',"; ?>
              ],
              label: '<?php echo $year->year - 1 ?>',
              borderColor: "rgba(104, 255, 0, 1)",
              fill: false
            },
            {
              data: [
		<?php foreach ($dataChartPrevYear as $key => $value) echo "'" . round($value) . "',"; ?>
              ],
              label: '<?php echo $year->year - 2 ?>',
              borderColor: "rgba(232, 142, 132, 1)",
              fill: false
            }
          ]
        },
        options: {
          title: {
            display: true,
            text: 'Total x Año'
          }
        }
      });
      
      <?php $totalYearSite = \App\Rooms::getPvpMonths_site($year->year); ?>
      var myBarChart = new Chart('barChartMonth', {
        type: 'line',
        data: {
          labels: [
            <?php foreach ($dataChartMonths as $key => $value) echo "'" . $key . "',"; ?>
          ],
          datasets: [
          {
            label: "RIAD",
            borderColor: '#6d5cae',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[1]}}],
          },
          {
            label: "Gloria",
            borderColor: 'green',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[3]}}],
          },
          {
            label: "Siloé",
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[5]}}],
          },
          {
            label: "Zahira",
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[6]}}],
          }
          
        ]

        },
      });
      $('.toggle-contab-site').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-ch-'+id).addClass('tr-close');
          $('.contab-rsite-'+id).addClass('tr-close');
        } else {
          $(this).addClass('open');
          $('.contab-ch-'+id).removeClass('tr-close');
        }
      });
      $('.toggle-contab').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-room-'+id).addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-room-'+id).removeClass('tr-close');
        }
      });

      $('.toggle-contab-extra').on('click',function(){
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-extras').addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-extras').removeClass('tr-close');
        }
      });

  
    new Chart(document.getElementById("pieOcuRiad"), {
        type: 'doughnut',
        data: {
            labels: ["Ocupac",'Libre'],
            datasets: [{
                label: '',
                data: [{{$pieOcuRiad}},{{100-$pieOcuRiad}}],
                backgroundColor: ['rgba(159, 90, 253, 1)'],
                borderColor: ['rgba(255, 255, 255 ,1)',],
                borderWidth: 2
            },
            ]
        },
        options: {
            rotation: 1 * Math.PI,/** This is where you need to work out where 89% is */
            circumference: 1 * Math.PI,/** put in a much smaller amount  so it does not take up an entire semi circle */
            legend: {display: false},
            tooltip: {enabled: false},
            cutoutPercentage: 70
        }
    });

    new Chart(document.getElementById("pieOcuGloria"), {
        type: 'doughnut',
        data: {
            labels: ["Ocupac",'Libre'],
            datasets: [{
                label: '',
                data: [{{$pieOcuGloria}},{{100-$pieOcuGloria}}],
                backgroundColor: ['rgba(159, 90, 253, 1)'],
                borderColor: ['rgba(255, 255, 255 ,1)',],
                borderWidth: 2
            },
            ]
        },
        options: {
            rotation: 1 * Math.PI,/** This is where you need to work out where 89% is */
            circumference: 1 * Math.PI,/** put in a much smaller amount  so it does not take up an entire semi circle */
            legend: {display: false},
            tooltip: {enabled: false},
            cutoutPercentage: 70
        }
    });
     new Chart(document.getElementById("pieOcuZahira"), {
        type: 'doughnut',
        data: {
            labels: ["Ocupac",'Libre'],
            datasets: [{
                label: '',
                data: [{{$pieOcuZahira}},{{100-$pieOcuZahira}}],
                backgroundColor: ['rgba(159, 90, 253, 1)'],
                borderColor: ['rgba(255, 255, 255 ,1)',],
                borderWidth: 2
            },
            ]
        },
        options: {
            rotation: 1 * Math.PI,/** This is where you need to work out where 89% is */
            circumference: 1 * Math.PI,/** put in a much smaller amount  so it does not take up an entire semi circle */
            legend: {display: false},
            tooltip: {enabled: false},
            cutoutPercentage: 70
        }
    });
     new Chart(document.getElementById("pieOcuSiloe"), {
        type: 'doughnut',
        data: {
            labels: ["Ocupac",'Libre'],
            datasets: [{
                label: '',
                data: [{{$pieOcuSiloe}},{{100-$pieOcuSiloe}}],
                backgroundColor: ['rgba(159, 90, 253, 1)'],
                borderColor: ['rgba(255, 255, 255 ,1)',],
                borderWidth: 2
            },
            ]
        },
        options: {
            rotation: 1 * Math.PI,/** This is where you need to work out where 89% is */
            circumference: 1 * Math.PI,/** put in a much smaller amount  so it does not take up an entire semi circle */
            legend: {display: false},
            tooltip: {enabled: false},
            cutoutPercentage: 70
        }
    });
    
    
    
    
  
    </script>
@endsection