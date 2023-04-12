<?php 
$total_coste = 0;
$total_pvp  = 0;
?>
<?php foreach ($alarms as $book): ?>
    <?php 
      $total_coste += $book->get_costeTotal();
      $total_pvp += $book->total_price;
    ?>
    <tr>
      @if($isMobile)
        <td class ="text-left static" style="width: 130px;color: black;overflow-x: scroll;    padding: 4px 3px !important; ">  
      @else
        <td class="text-left" >
      @endif
                <?php if ($book->agency != 0): ?>
                    <img class="img-agency" src="/pages/<?php  echo strtolower($book->getAgency($book->agency)) ?>.png" />
                   <?php endif ?>
                <a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
                    <?php  echo $book->customer->name ?>
                </a>
                <?php if (!empty($book->book_owned_comments) && $book->promociones != 0 ): ?>
                    <img src="/img/oferta.png" class="img-oferta" title="<?php echo $book->book_owned_comments ?>">
                <?php endif ?>
        </td>
      @if($isMobile)
        <td class="text-center first-col" style="padding-right:13px !important;padding-left: 135px!important">   
      @else
        <td class="text-center" >
      @endif
            <!-- apto -->
            <?php echo $book->room->nameRoom ?>
        </td>
        <td class="text-center">
            {{convertDateToShow_text($book->start)}} - {{convertDateToShow_text($book->finish)}}
        </td>
        <td class="text-center PVP" style="border-left: 1px solid black;">

          <?php echo number_format($book->total_price,0,',','.') ?> €</b>
        </td>
        <td class="text-center coste bi " style="border-left: 1px solid black;">

          <?php echo number_format($book->get_costeTotal(),2,',','.') ?> €</b>
        </td>
        <?php $inc_percent = $book->get_inc_percent();?>
        <?php if(round($inc_percent) <= $percentBenef && round($inc_percent) > 0): ?>
            <?php $classDanger = "background-color: #f8d053!important; color:black!important;" ?>
        <?php elseif(round($inc_percent) <= 0): ?>
            <?php $classDanger = "background-color: red!important; color:white!important;" ?>
        <?php else: ?>
            <?php $classDanger = "" ?>
        <?php endif; ?>
        <td class="text-center beneficio bf " style="border-left: 1px solid black; <?php echo $classDanger ?>">
                <?php echo number_format($inc_percent,0)."%" ?>
        </td>

        <td class="text-center " >
          <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-default toggleAlertLowProfits" type="button" data-toggle="tooltip" title="" data-original-title="Activa / Desactiva el control de Beneficio para este registro." data-sended="1">
            @if($book->has_low_profit)
              <i class="fa fa-bell-slash" aria-hidden="true"></i>
            @else
              <i class="fa fa-bell" aria-hidden="true"></i>
            @endif
          </button> 
        </td>
    </tr>
<?php endforeach ?>
          
<script type="text/javascript">
  $(document).ready(function() {
    $('#alarms_totalPVP').text("<?php echo $total_pvp.' €';?>");
    $('#alarms_totalCosteTotal').text("<?php echo $total_coste.' €';?>");
  });
</script>