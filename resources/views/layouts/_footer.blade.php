<?php
if (!isset($oContents)) $oContents = new App\Contents();
if (!isset($site_id))   $site_id = config('app.site_id',1);
if (!isset($roomsUrl))  $roomsUrl = App\RoomsType::getMenuRooms();

$footerContent = $oContents->getContentByKey('footer');
?>

<style>
  .grecaptcha-badge {
    visibility: hidden;
  }
</style>
<footer id="footer" >	
  <!-- Copyrights
        ============================================= -->
  <div id="copyrights"  class="footer-area  loadBackground"  data-img3="" data-img2="">
    <div class="capa-blanca">
      @if($site_id>0 && false)
     @include('frontend.blocks.footers.content_'.$site_id)
     @endif
    </div>
  </div><!-- #footer end -->
  <div class=" copyright">
    Copyrights © {{date('Y')}} Todos los derechos reservados.
  </div>
</footer>

<script type="text/javascript">
  var LoadJs = "{{ getCloudfl(asset('/js/scripts-footer-min-v2.js'))}}";
  function LoadImgs() {
    console.log('LoadImgsBackground');
    var w_screen = $(window).width();
    $(".loadJS").each(function (index) {
      var img = $( this ).data('src');
      if (w_screen<481) var img = $( this ).data('src2');
      $(this).attr('src', img);
    });
  }
  function LoadImgsBackground() {
     console.log('LoadImgsBackground');
    $(".loadJSBackground").each(function (index) {
      $(this).css("background-image", "url('" + $(this).data('src') + "')");
    });
    
    var w_screen = $(window).width();
   
    $('.loadBackground').each(function( index ) {
      var img = imgDefualt = $( this ).data('img');
      if (w_screen<481) var img = $( this ).data('img2');
      if (w_screen>780) var img = $( this ).data('img3');
        
      if (!img) img = imgDefualt;
        
      $( this ).css('background-image',img);
    });
    
  }

</script>