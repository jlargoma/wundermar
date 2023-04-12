<style>
  .btn-primary{
    background-color: #295d9b!important;
  }
  @media only screen and (max-width: 425px){
    .buttons-box{
      width: 98%;
      overflow: auto;
    }
    .buttons-lst{
      width: 95em;
    }
    .btn-contabilidad {
      margin: 0 1px;
    }
    .widget-loader-bar{
      height: 110px !important;
    }
    .widget-loader-bar h5 {
      font-size: 17px;
      line-height: 1.5;
    }
  }
</style>
<?php
$url = substr(Request::path(), 6);
$posicion = strpos($url, '/');
if ($posicion > 0) {
  $url = substr($url, 0, $posicion);
} else {
  $url;
};
$uRole = getUsrRole();
$items = [
  ['k'=>1,'url'=>'contabilidad','title'=>'Estadisticas','id'=>null],  
  ['k'=>2,'url'=>null,'title'=>'Vtas X Agenc','id'=>'booking_agency_details'],  
  ['k'=>3,'url'=>'gastos','title'=>'Gastos','id'=>null],    
  ['k'=>4,'url'=>'ingresos','title'=>'Ingresos','id'=>null],   
  ['k'=>5,'url'=>'banco','title'=>'Banco','id'=>null],   
  ['k'=>6,'url'=>'caja','title'=>'Caja','id'=>null],   
  ['k'=>7,'url'=>'perdidas-ganancias','title'=>'CTA P &amp; G','id'=>null],   
  ['k'=>8,'url'=>'limpiezas','title'=>'Limpiezas','id'=>null],   
  ['k'=>9,'url'=>'orders-payland','title'=>'PAYLAND','id'=>null],   
  // ['k'=>10,'url'=>'excursiones','title'=>'EXCURSIONES','id'=>null],   
];
$toRemove = [];
if ($uRole !== 'admin'){
  $toRemove = [1,2,5,7];
}
if ($uRole == 'limpieza'){
  $toRemove[] = 3;
  $toRemove[] = 4;
  $toRemove[] = 9;
}
if ($uRole == 'recepcionista'){
  $toRemove[] = 3;
  $toRemove[] = 4;
  $toRemove[] = 8;
}
foreach ($items as $k=>$v){
  if (in_array($v['k'], $toRemove)){
    unset($items[$k]);
  }
  
}

?>
<div class="buttons-box">
  <div class="buttons-lst">
  <?php foreach ($items as $item): ?>
    <div class="btn-contabilidad">
      <?php if ($item['url'] == $url): ?>
        <button class="btn btn-md text-white active"  disabled>{{$item['title']}}</button>
      <?php else: ?>
        <?php if($item['id']): ?>
          <button id="{{$item['id']}}" class="btn btn-primary">Vtas X Agenc</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary" href="{{url('/admin/'.$item['url'])}}">{{$item['title']}}</a>
        <?php endif ?>	
      <?php endif ?>	
    </div>
  <?php endforeach; ?>
  </div>
</div>
@include('backend.sales._vtas-x-agencia')