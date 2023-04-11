<div class="box">
  <h2 class="text-center">Stripe y pagos</h2>
  <div class="row">
    <div class="col-md-6 col-xs-12 overflow-x">
      <table class="table table-hover  table-responsive">
        <thead>
          <tr>
            <th class="text-center bg-complete text-white" style="width: 1%" colspan="2">
              Condiciones cobro link stripe
            </th>
          </tr>
          <tr>
            <th class="text-center" style="width: 1%" rowspan="2">
              PORCENTAJE
            </th>
            <th class="text-center" style="width: 1%" rowspan="2">DIAS
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (\App\RulesStripe::all() as $key => $rule): ?>
            <tr>
              <td class="text-center" style="border-left: 1px solid #48b0f7">
                <input class="rules percent-<?php echo $rule->id ?>" type="text" name="cost"
                       data-id="<?php echo $rule->id ?>" value="<?php echo $rule->percent ?>"
                       style="width: 100%;text-align: center;">
              </td>
              <td class="text-center">
                <input class="rules days-<?php echo $rule->id ?>" type="text" name="cost"
                       data-id="<?php echo $rule->id ?>" value="<?php echo $rule->numDays ?>"
                       style="width: 100%;text-align: center;">
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <div class="col-md-6 col-xs-12 overflow-x">
      <table class="table table-hover  table-responsive">
        <thead>
          <tr>
            <th class="text-center bg-complete text-white" style="width: 1%" colspan="2"> Dias del
              segundo pago
            </th>
          </tr>
          <tr>
            <th class="text-center" style="width: 1%" rowspan="2"> DIAS</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (\App\DaysSecondPay::all() as $key => $day): ?>
            <tr>
              <td class="text-center" style="border-left: 1px solid #48b0f7">
                <input class="daysSecondPayment" type="number" name="days"
                       data-id="<?php echo $day->id ?>" value="<?php echo $day->days ?>"
                       style="width: 100%;text-align: center;">
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>