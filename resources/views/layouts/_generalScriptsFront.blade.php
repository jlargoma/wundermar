<!-- before general -->

<!--<script src='https://www.google.com/recaptcha/api.js?render=6LdOoYYUAAAAAPKBszrHm6BWXPE8Gfm3ywnoOEUV' async="async"></script>-->

<?php /* view para todos los scripts generales de la pagina */ ?>

@yield('scripts')

<style>
.show-mobile{
   display: none;
 }
@media only screen and (max-width: 426px) {
  .hidden-mobile{
    display: none;
  }
  .show-mobile{
    display: block;
  }
}


</style>

  <script type="text/javascript">
    $(document).ready(function () {
      $('div.bg-img img').attr('style', 'max-width:none !important;margin-left: -67.2vw;');
      setTimeout(
              function () {
                var my_awesome_script = document.createElement('script');
                my_awesome_script.setAttribute('src', "{{ getCloudfl(asset('/js/scripts-ext-v2.js'))}}");
                document.body.appendChild(my_awesome_script);
                var lightslider = document.createElement('script');
                lightslider.setAttribute('src', "{{ getCloudfl(assetV('/frontend/vendor/lightslider-master/dist/js/lightslider.min.js'))}}");
                document.body.appendChild(lightslider);

                var my_awesome_style = document.createElement('link');
                my_awesome_style.setAttribute('href', "{{ getCloudfl(assetV('/frontend/css/responsive-mobile.css'))}}");
                my_awesome_style.setAttribute('type', 'text/css');
                my_awesome_style.setAttribute('rel', 'stylesheet');
                document.body.appendChild(my_awesome_style);

                var recaptcha_script = document.createElement('script');
                recaptcha_script.setAttribute('src', "https://www.google.com/recaptcha/api.js?render=6LdOoYYUAAAAAPKBszrHm6BWXPE8Gfm3ywnoOEUV");
                document.body.appendChild(recaptcha_script);
                $('.carousel-caption').css('display','block');
                
                var my_awesome_script = document.createElement('script');
                my_awesome_script.setAttribute('src', "{{ getCloudfl(asset('/js/booking.js'))}}");
                document.body.appendChild(my_awesome_script);

              }, 100);
      $('div.bg-img img').attr('style', 'max-width:none !important;');
      
      
      $('#confirmBookStatic').on('click', function(event){
         event.preventDefault();
         $.post('/static-token', function (data) {
            $('#_static_token').val(data);
            $('#form-book-apto-lujo').submit()
          });
         
      });
        
    $('div.bg-img').attr('style', 'background:none !important;');
    $('div#boxgallery span.prev, div#boxgallery span.next').attr('style', 'z-index:10000;');
    $('div#blank_loader').fadeOut(500);
    });
    
    
    
  </script>

</body>