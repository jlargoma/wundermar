
<div class="modal fade slide-up in" id="modalOtasDisc" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-md">
    <div class="modal-content-wrapper">
      <div class="modal-content" style="padding: 7px;   font-size: 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
          <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
        </button>
        <h4 class="text-center">Revisar Channels: </h4>


        <?php
        $allSites = App\Sites::allSites();
        foreach ($otasDisconect as $site => $ota) {
          echo "<h2>".show_isset($allSites, $site)."</h2>";
          echo '<ul>';
          foreach ($ota as $item) {
            echo "<li>$item</li>";
          }
          echo '</ul>';
        }
        ?>
      </div>
    </div>
  </div>
</div>


