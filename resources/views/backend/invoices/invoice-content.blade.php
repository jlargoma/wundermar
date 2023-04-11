<?php function printInvoiceContent($oInvoice,$items,$site,$bkg_data=null){ ?>
<div class="panel panel-default main">
  <div class="panel-heading">
    <div class="clearfix">
      <div class="col-xs-6">
        <img src="{{url('/img/riad/logo_riad.png')}}" class="img-responsive">
        <div class="inv_numb"><?php echo $oInvoice->num ?></div>
        <b>
          <?php 
          $f = explode(' ',$oInvoice->created_at); 
          echo convertDateToShow($f[0],true);
          ?>
        </b><br/>
        <b>RIAD PUERTAS DEL ALBAICÍN, S.L.</b><br/>
        <?php
        if (trim($oInvoice->nif_business) != '') echo '<b>'. $oInvoice->nif_business .'</b><br/>';
        if (trim($oInvoice->address_business) != '') echo '<b>'. $oInvoice->address_business .'</b><br/>';
        if (trim($oInvoice->zip_code_business) != '') echo '<b>'. $oInvoice->zip_code_business .'</b><br/>';
        ?>
      </div>
      <div class="col-xs-3">

      </div>
      <div class="col-xs-5 info-empresa">
        <?php
        if (trim($oInvoice->name_business) != '') echo '<b>'. $oInvoice->name_business .'</b><br/>';
        if (trim($oInvoice->reat_business) != '') echo '<b>REAT</b> '. $oInvoice->reat_business .'<br/>';
        if (trim($oInvoice->url_business) != '') echo $oInvoice->url_business .'<br/>';
        ?>
      </div>
    </div>
  </div>
  <!--row-->
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-9 col-md-9 col-sm-9">
        <h3><?php echo ucfirst($oInvoice->name) ?></h3>
          <?php
          if (trim($oInvoice->nif) != '') echo '<b>DNI</b> '. $oInvoice->nif .'<br/>';
          if (trim($oInvoice->email) != '') echo '<b>Email</b> '. $oInvoice->email .'<br/>';
          if (trim($oInvoice->phone) != '') echo '<b>Teléfono</b> '. $oInvoice->phone .'<br/>';
          if (trim($oInvoice->address) != '') echo '<b>Dirección</b> '. $oInvoice->address .'<br/>';
          if (trim($oInvoice->zip_code) != '') echo '<b>C. Postal</b> '. $oInvoice->zip_code .'<br/>';
          ?>
      </div>
    </div>
      <!--row-->
    @if($bkg_data)
    <table class="table">
      <thead>
        <tr>
          <th class="text-center">Nº Hab</th>
          <th class="text-center">Personas</th>
          <th class="text-center">Fecha Entrada</th>
          <th class="text-center">Fecha Salida</th>
        </tr>
      </thead>
      <tbody>
          <tr>
            <td class="text-center"><?php echo $bkg_data['room']; ?></td>
            <td class="text-center"><?php echo $bkg_data['pax']; ?></td>
            <td class="text-center"><?php echo $bkg_data['start']; ?></td>
            <td class="text-center"><?php echo $bkg_data['finish']; ?></td>
          </tr>
      </tbody>
    </table>
    @endif
    <!--row-->
    <table class="table">
      <thead>
        <tr>
          <th>Descripción</th>
          <th class="text-center">IVA</th>
          <th class="text-right">Importe</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $subtotal = 0;
        if ($items):
          foreach ($items as $item):
            $detail = convertBold($item['detail']);
            $iva = floatval($item['iva']);
            $price = floatval($item['price']);
            if ($iva > 0) {
              $sinIvaPVP = removeIVA($price, $iva);
              $subtotal += $sinIvaPVP;
              echo '<tr><td>' . $detail . '</td><td class="text-center">' . $iva . '%</td><td class="text-right">' . moneda($sinIvaPVP, true, 2) . '</td></tr>';
            } else {
              $subtotal += $price;
              echo '<tr><td>' . $detail . '</td><td class="text-center">--</td><td class="text-right">' . moneda($price, true, 2) . '</td></tr>';
            }
          endforeach;
        endif;
        ?>
        <tr><td colspan="3"><br/><br/></td></tr>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-right" colspan="2">Total Neto</th>
          <th class="text-right">{{moneda($subtotal,true,2)}}</th>
        </tr>
        @if($oInvoice->total_price>$subtotal)
        <tr>
          <th class="text-right" colspan="2">IVA</th>
          <th class="text-right">{{moneda($oInvoice->total_price-$subtotal,true,2)}}</th>
        </tr>
        @endif
        <tr>
          <th class="text-right" colspan="2">Total</th>
          <th class="text-right">{{moneda($oInvoice->total_price,true,2)}}</th>
        </tr>
      </tfoot>
    </table>
    <br/>
  </div>
  <div class="panel-footer">
    <p class="text-center">{{$site['name']}}</p>
  </div>
</div>
<?php } ?>