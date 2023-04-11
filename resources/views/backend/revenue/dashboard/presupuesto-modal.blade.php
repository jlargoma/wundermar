<?php
$meses = $months;
$Y = $year; 
?>

@foreach ($aSites as $S=>$v)
<div class="presupuesto presup_{{$S}} table-responsive" >
  <table class="table">
    <tr class="grey">
      <td>CONCEPTO</td>
      <td class="tcenter">ANUAL</td>
      @foreach ($meses as $M=>$v2)
      <td class="tcenter">{{$v2}} (€)</td>
      @endforeach
    </tr>
    <?php
    $aTotals = [];
    $yearTotals = 0;
    foreach ($FCItems as $key => $concept):
      ?>
      <tr class="borders">
        <td>{{$concept}}</td>
        <?php
        $tYear = 0;
        $aux_attr = 'data-k="' . $key . '" data-site="' . $S . '" data-y="' . $Y . '"  ';
        $auxValues = [];
        if (isset($fixCosts[$S][$key])) {
          $val = $fixCosts[$S][$key];
          $M = 0;
          if (!isset($aTotals["mdlFC$S-$M-$Y"]))
            $aTotals["mdlFC$S-$M-$Y"] = 0;
          $aTotals["mdlFC$S-$M-$Y"] += $val[0];
          $tYear = array_sum($val);
          $yearTotals += $tYear;
          ?>
          
          <?php
          foreach ($meses as $M => $v2) {
            if (!isset($aTotals["mdlFC$S-$M-$Y"]))
              $aTotals["mdlFC$S-$M-$Y"] = 0;
            $aTotals["mdlFC$S-$M-$Y"] += $val[$M];
            $auxValues[$M] = ($val[$M]) ? $val[$M] : '';
          }
        } else {
          foreach ($meses as $M => $v2) {
           $auxValues[$M] = '';
          }
        }
        ?>
        <td class="tcenter bold fixColTtalMdl {{$S}} {{$key}}" data-v="{{$tYear}}">{{moneda($tYear)}}</td>
        @foreach ($auxValues as $M => $v2)
        <td class="text-right"><input class="fixcostMdl fixCol{{$S.$Y.$M}} {{$S.$key}}" <?php echo $aux_attr; ?> data-m="{{$M}}" value="{{$v2}}"></td>
        @endforeach
      </tr>
      <?php
    endforeach;
    ?>
    <tr class="grey">
      <td>TOTALES</td>
      <td id="fixColTtalMdl{{$S}}"  class="tcenter">{{moneda($yearTotals)}}</td>
      @foreach ($meses as $M=>$v2)
      <td id="mdlFC{{$S.'_'.$M.'_'.$Y}}" class="tcenter">
        <?php
        if (isset($aTotals["mdlFC$S-$M-$Y"]))
          echo moneda($aTotals["mdlFC$S-$M-$Y"]);
        ?>
      </td>
      @endforeach
    </tr>
  </table>
</div>

@endforeach
<script>
  $('.fixcostMdl').on('click', function(){
    
    $('.fixcostMdl').each(function( index ) {
      var obj = $(this);
      if (obj.val() == ''){
        obj.val(obj.data('old'));
      }
    });
    var obj = $(this);
    obj.data('old',obj.val());
    obj.val('');
  });

  $('.fixcostMdl').on('change', function(){
    var obj = $(this);
    var value = obj.val();
    var key  = obj.data('k');
    var site = obj.data('site');
    var y = obj.data('y');
    var m = obj.data('m');
    if (value == ''){
      obj.val(obj.data('old'));
      return ;
    }
    var data = {
      val: obj.val(),
      _token: "{{csrf_token()}}",
      site: site,
      key: key,
      y: y,
      m: m,
    }
    var ktotal = '#tFC' + site + '_' + y + '' + m;
//    var ktotalYear = '#tFC' + obj.data('site') + '_' + obj.data('y') + '0';
//    var kYear = '#FC' + obj.data('site') + '_' + obj.data('k') + '_' + obj.data('y');
    $.post("/admin/revenue/upd-fixedcosts", data).done(function (resp) {
      if (resp.status == 'OK') {
        window.show_notif('Registro modificado', 'success', '');
        $(ktotal).text(resp.totam_mensual);
        
        //---------------------------------------------------------//
        var tCol = 0;
        $('.fixCol'+site+y+m).each(function( index ) {
          if ($(this).val()) tCol += parseInt($(this).val());
        });
        $('#mdlFC'+site+'_'+m+'_'+y).text(tCol+' €');
        //---------------------------------------------------------//
        var tCol = 0;
        $('.fixcostMdl.'+site+key).each(function( index ) {
          console.log($(this).val());
          if ($(this).val())  tCol += parseInt($(this).val());
        });
        $('.fixColTtalMdl.'+site+'.'+key).text(tCol+' €');
        $('.fixColTtalMdl.'+site+'.'+key).data('v',tCol);
        //---------------------------------------------------------//
        var tCol = 0;
        $('.fixColTtalMdl.'+site).each(function( index ) {
          if ($(this).data('v')) tCol += parseInt($(this).data('v'));
        });
        $('#fixColTtalMdl'+site).text(tCol+' €');
        //---------------------------------------------------------//
        
      } else {
        window.show_notif(resp, 'danger', '');
      }
    });
  });
  </script>