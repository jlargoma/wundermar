<style>
  @media only screen and (max-width: 780px) {
    .box-btn-sites{
      max-width: 98%;
      overflow: auto;
    }
    .btn-sites{
      width: 50em;
    }
  }
</style>



<div class="box-btn-sites col-xs-12">
  <div class="btn-sites">
    <?php 
    $bSite = \App\Sites::all();
    $s_active=''; 
    ?>
    <button class="btn btn-info select_site active" data-k="0">
      TODOS
    </button>
    @foreach($bSite as $item)
    <button class="btn btn-info select_site" data-k="{{$item->id}}">
      <?php echo $item->name ?>
    </button>
    @endforeach
  </div>
</div>


<div class="contenedor">
  <div class="table-responsive ">
    <table class="table table-resumen table-excel">
      <thead>
        <tr class="resume-head">
          <th class="thSpecial" colspan="2"></th>
          <td class="first-col"></td>
          @if($aLstDays)
          @foreach($aLstDays as $d=>$w)
          <th>
            <?php 
            $day = explode('_',$d);
            echo $w.'<br/>'.$day[0]; ?>
            </th>
          @endforeach
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($otas as $ch=>$nro)
        <?php $sID = 'disponib_'.$otaSite[$ch]; ?>
        <tr class="tDispon disponib_0 {{$sID}}">
          <td rowspan="3" class="tdSpecial td1">{{show_isset($chNames,$ch)}}</td>
          <td class="tdSpecial td2 totals">Total</td>
          <td class="totals first-col"></td>
          @foreach($aLstDays as $d=>$w)
          <td class="totals vals" data-v="{{$nro}}">{{$nro}}</td>
          @endforeach
        </tr>
        <tr class="tDispon disponib_0 {{$sID}}">
          <td class="tdSpecial td2">Libres</td>
          <td class="first-col"></td>
          @foreach($listDaysOtas[$ch] as $avail)
          <td class="avails {{($avail>0) ? 'number' : ''}}" data-v="{{$avail}}">{{($avail>0) ? $avail : '-'}}</td>
          @endforeach
        </tr>
        <tr class="tDispon disponib_0 {{$sID}}">
          <td class="tdSpecial td2">Ocupadas</td>
          <td class="first-col"></td>
          @foreach($listDaysOtas[$ch] as $avail)
          <td class="ocupadas" data-v="{{$nro-$avail}}">{{$nro-$avail}}</td>
          @endforeach
        </tr>
        @endforeach
        <tr class="tr-total disponib_total">
          <td rowspan="3" class="tdSpecial td1">TOTAL</td>
          <td class="tdSpecial td2 totals">Total</td>
          <td class="first-col totals"></td>
          @foreach($aLstDays as $d=>$w)
          <td class="totals  vals">{{$totalOtas}}</td>
          @endforeach
        </tr>
        <tr class="disponib_libres">
          <td class="tdSpecial td2">Libres</td>
          <td class="first-col"></td>
          @foreach($listDaysOtasTotal as $v)
          <td class="avails  {{($v>0) ? 'number' : ''}}">{{($v>0) ? $v : '-'}}</td>
          @endforeach
        </tr>
        <tr class="disponib_ocupadas">
          <td class="tdSpecial td2">Ocupadas</td>
          <td class="first-col"></td>
          @foreach($listDaysOtasTotal as $v)
          <td class="ocupadas">{{$totalOtas-$v}}</td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('.select_site').on('click', function (event) {
      $('.select_site').removeClass('active');
      var cName = '.disponib_'+$(this).data('k');
      console.log(cName);
      $('.tDispon').hide();
      $(cName).show();
      /**********************************************************/
      var totals = new Array();
      $(cName).each(function(i,j){
        $(this).find('.vals').each(function(i2,j2){
          if (typeof totals[i2] == "undefined") {
            totals[i2] = 0;
          }
          totals[i2] = totals[i2]+$(this).data('v');
        });
      });
      $('.disponib_total').find('.vals').each(function(i,j){
        $(this).text(totals[i]);
      });
      
      /**********************************************************/
      var totals = new Array();
      $(cName).each(function(i,j){
        $(this).find('.avails').each(function(i2,j2){
          if (typeof totals[i2] == "undefined") {
            totals[i2] = 0;
          }
          totals[i2] = totals[i2]+$(this).data('v');
        });
      });
      $('.disponib_libres').find('.avails').each(function(i,j){
        $(this).text(totals[i]);
      });
      /**********************************************************/
      var totals = new Array();
      $(cName).each(function(i,j){
        $(this).find('.ocupadas').each(function(i2,j2){
          if (typeof totals[i2] == "undefined") {
            totals[i2] = 0;
          }
          totals[i2] = totals[i2]+$(this).data('v');
        });
      });
      $('.disponib_ocupadas').find('.ocupadas').each(function(i,j){
        $(this).text(totals[i]);
      });
    });
  });
</script>