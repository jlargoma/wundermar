<!DOCTYPE html>
<html lang="es">
  <head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   @include('backend.invoices.invoice-style')
  </head>
  <body>
    <div class="content">
      @include('backend.invoices.invoice-content')
      <?php 
      
        if($oInvoices){
          foreach($oInvoices as $oInvoice){
            $items = $oInvoice->getMetaContent('items');
            if ($items) $items = unserialize($items);
            $siteData = \App\Sites::siteData($oInvoice->site_id);
            echo '<div class="whatever">';
            printInvoiceContent($oInvoice,$items,$siteData);
            echo '</div>';
          }
        }
      ?>
    </div>

  </body>
</html>

