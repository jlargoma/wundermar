<div class="table-responsive">
  <table class="tableMonths" id="MonthsPresup">
    <tr>
      <td data-k="0" class="sm <?php if($month == 0) echo 'active' ?> ">ANUAL</td>
      @foreach($months as $k=>$v)
      <td data-k="{{$k}}" class="sm <?php if($month == $k) echo 'active' ?> ">{{$v}}</td>
      @endforeach
    </tr>
  </table>  
</div>

<div  id="blockPresup" ><?php echo $presupuesto; ?></div>