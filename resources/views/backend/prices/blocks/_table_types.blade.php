<div class="row">
  <div class="col-md-12">
    
    <div class="clearfix"></div>
    <div class="tab-content">

      <div class="col-md-12 table-responsive">
        <table class="table table-hover  table-responsive" >
          <thead>
            <tr>
              <th class ="text-center bg-white text-complete" style="width: 5%" rowspan="2">  </th>
              <?php foreach ($seasons as $key => $season): ?>
                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" style="width: 25%" colspan="3"> <?php echo $season->name ?> </th>
              <?php endforeach ?>
            </tr>
            <tr>                          
              <?php foreach ($seasons as $key => $season): ?>
                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" style="width: 5%" >PVP</th>
                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" style="width: 5%">Cost</th>
                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" style="width: 5%">% Ben</th>
              <?php endforeach ?>
            </tr>
          </thead>
          <tbody>
            @for($K=1;$K<11;$K++)
            <tr>
              <td>{{$K}}</td>
              @foreach($seasons as $season)
              <?php $kPrice = $K.'-'.$season->id; ?>
                <td class="text-center">
                  <input class="editable price-{{$kPrice}}" type="text" name="price" data-id="{{$kPrice}}" value="{{$allPrices[$kPrice]['pvp']}}" style="width: 100%;text-align: center;border-style: none none">
                </td>
                <td class="text-center">
                  <input class="editable cost-{{$kPrice}}" type="text" name="cost" data-id="{{$kPrice}}" value="{{$allPrices[$kPrice]['cost']}}" style="width: 100%;text-align: center;border-style: none none">
                </td>
                <td class="text-center" style="border-right: 1px solid #48b0f7">
                    <?php echo round($allPrices[$kPrice]['benef']) ?>%
                </td>
              @endforeach
            </tr>
            @endfor
           
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>