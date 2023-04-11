<!-- before general -->

<script type="text/javascript" src="{{ asset('/frontend/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/js/plugins.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/js/functionsTest.js')}}"></script>

<script type="text/javascript" src="{{ asset('/js/flip.min.js')}}"></script>

<script type="text/javascript" src="{{asset('/frontend/js/components/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/aos/2.1.1/aos.js')}}"></script>

<script type="text/javascript" src="{{asset('/frontend/js/jquery.flip/1.1.2/jquery.flip.min.js')}}"></script>

<script src='https://www.google.com/recaptcha/api.js?render=6LdOoYYUAAAAAPKBszrHm6BWXPE8Gfm3ywnoOEUV'></script>

<?php /* view para todos los scripts generales de la pagina */ ?>

<!-- general scripts -->

<script type="text/javascript">
  /* Calendario */
  $(function () {
    $(".daterange1").daterangepicker({
      "buttonClasses": "button button-rounded button-mini nomargin",
      "applyClass": "button-color",
      "cancelClass": "button-light",
      locale: {
        format: 'DD MMM, YY',
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Custom",
        "daysOfWeek": [
          "Do",
          "Lu",
          "Mar",
          "Mi",
          "Ju",
          "Vi",
          "Sa"
        ],
        "monthNames": [
          "Enero",
          "Febrero",
          "Marzo",
          "Abril",
          "Mayo",
          "Junio",
          "Julio",
          "Agosto",
          "Septiembre",
          "Octubre",
          "Noviembre",
          "Diciembre"
        ],
        "firstDay": 1,
      },
    });
  });

  function unflip() {

    $("#content-book-response").flip(false);
    $('#content-book-response .back').empty();
  }

  // function calculateDays(date1, date2){
  //  var result = "";
  //  $.post( '/getDiffIndays' , { date1: date1, date2: date2}, function(data) {
  //     result = data;
  //  });

  //  return result;
  // }

  $(document).ready(function () {

    $(".only-numbers").keydown(function (e) {
      // Allow: backspace, delete, tab, escape, enter and .
      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
          // Allow: Ctrl+A, Command+A
          (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
          // Allow: home, end, left, right, down, up
          (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
      }
      // Ensure that it is a number and stop the keypress
      if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
      }
    });


    $("#content-book-response").flip({
      trigger: 'manual'
    });


    $('#form-book-apto-lujo').submit(function (event) {

      event.preventDefault();


      var _token = $('input[name="_token"]').val();
      var name = $('input[name="name"]').val();
      var email = $('input[name="email"]').val();
      var phone = $('input[name="telefono"]').val();
      var date = $('input[name="date"]').val();
      var quantity = $('select[name="quantity"]').val();
      var apto = $('input:radio[name="apto"]:checked').val();
      var luxury = $('input:radio[name="luxury"]:checked').val();
      var parking = 'si';
      var comment = $('textarea[name="comment"]').val();

      var url = $(this).attr('action');

      var dateAux = date;

      var arrayDates = dateAux.split('-');
      var res1 = arrayDates[0].replace("Abr", "Apr");
      var date1 = new Date(res1);
      var start = date1.getTime();

      var res2 = arrayDates[1].replace("Abr", "Apr");
      var date2 = new Date(res2);
      var timeDiff = Math.abs(date2.getTime() - date1.getTime());

	if(apto == '' || typeof apto == "undefined"){
		alert('El tipo de Apartamento es requerido');
		return;
	}

      $.post('/getDiffIndays', {date1: arrayDates[0], date2: arrayDates[1]}, function (data) {
        var diffDays = data.diff;
        var minDays = data.minDays;


        if (diffDays >= 2) {
		 if (data.specialSegment != false && minDays>diffDays) {
		    $('#content-book-response .back').empty();
		    alert('ESTANCIA MÍNIMA EN ESTAS FECHAS:' + minDays + ' DÍAS');
		    /*
              $('#content-book-response .back').append('<h2 class="text-center text-white white" ' +
                  'style="line-height: 1; letter-spacing: -1px;">ESTANCIA M&Iacute;NIMA EN ' +
                  'ESTAS ' +
                  'FECHAS: ' + minDays + ' D&Iacute;AS</h2>');
                  */
               
            //$('#content-book-response .back').append(data);
            //$("#content-book-response").flip(true);
         } else {
          $.post(url, {
            _token: _token,
            name: name,
            email: email,
            phone: phone,
            fechas: data.dates,
            quantity: quantity,
            apto: apto,
            luxury: luxury,
            parking: parking,
            comment: comment
          }, function (data) {

            $('#content-book-response .back').empty();
            $('#content-book-response .back').append(data);
            $("#content-book-response").flip(true);
           
          });
		}
        } else {
          alert('Estancia minima ' + minDays + ' NOCHES')
        }


      });


    });

     <?php if ($mobile->isMobile() || $mobile->isTablet()): ?>
          $('#banner-offert, .menu-booking').click(function (event) {
            $('#content-book').show('400');
            $('#banner-offert').hide();
            $('#line-banner-offert').hide();
            $('#desc-section').hide();
            $('section#content').css('z-index', '20000');
            $('html, body').animate({
              /*scrollTop: $("section#content").offset().top*/
              scrollTop: $("#content-book").offset().top + 10
            }, 2000);
          });

            $('#close-form-book').click(function (event) {
              $('#banner-offert').show();
              $('#line-banner-offert').show();
              $('#content-book').hide('100');
              $('#desc-section').show();
              $('section#content').css('z-index', '0');
              $('#content-book-payland').html('');
              unflip();

              $('html, body').animate({
                scrollTop: $("body").offset().top
              }, 2000);
            });

            $('#confirm-reserva').click(function () {
              $('html, body').animate({
                /*scrollTop: $("section#content").offset().top*/
                scrollTop: $("#content-book-response").offset().top - 30
              }, 2000);
            });


     <?php else: ?>
              $('#banner-offert, .menu-booking').click(function (event) {
                $('#content-book').show('400');
                $('#banner-offert').hide();
                $('#line-banner-offert').hide();

                $('html, body').animate({
                  scrollTop: $("#content-book").offset().top - 85
                }, 2000);
              });


            $('#close-form-book').click(function (event) {
              $('#banner-offert').show();
              $('#line-banner-offert').show();
              $('#content-book').hide('100');
              $('#content-book-payland').html('');
              unflip();
              $('html, body').animate({
                scrollTop: $("body").offset().top
              }, 2000);
            });
     <?php endif; ?>

      $('.daterange1').change(function (event) {
        var date = $(this).val();

        var arrayDates = date.split('-');

        var date1 = new Date(arrayDates[0]);
        var date2 = new Date(arrayDates[1]);
        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        console.log(diffDays);
        if (diffDays < 2) {
          $('.min-days').show();
        } else {
          $('.min-days').hide();
        }

      });

  });

  /* Para pagina de apartamentos */

  $('span.close').click(function (event) {
    $('#content-form-book').hide('400');
    unflip();
    $('html, body').animate({
      scrollTop: $("body").offset().top
    }, 2000);
    $('#fixed-book').fadeIn();
  });

  <?php if (!$mobile->isMobile()): ?>

  $('#showFormBook, a.menu-booking-apt').click(function (event) {
    $('#content-form-book').slideDown('400');
    $('html,body').animate({
          scrollTop: $("#content-form-book").offset().top - 80
        },
        'slow');
  });
  <?php else: ?>

  $('#showFormBook, a.menu-booking-apt').click(function (event) {
    $('#content-form-book').slideDown('400');
    $('html,body').animate({
          scrollTop: $("#content-form-book").offset().top
        },
        'slow');

    $('#fixed-book').fadeOut();

  });
   <?php endif; ?>
</script>

<!-- after general -->

<?php if (!$mobile->isMobile()): ?>
<!--<script type="text/javascript">
          var tpj=jQuery;

          var revapi27;
          tpj(document).ready(function() {
            if(tpj("#rev_slider_27_1_home").revolution == undefined){
              revslider_showDoubleJqueryError("#rev_slider_27_1");
            }else{
              revapi27 = tpj("#rev_slider_27_1_home").show().revolution({
                sliderType:"standard",
                jsFileLocation:"/frontend/include/rs-plugin/js/",
                sliderLayout:"fullscreen",
                dottedOverlay:"none",
                delay:9000,
                navigation: {
                  keyboardNavigation:"off",
                  keyboard_direction: "horizontal",
                  mouseScrollNavigation:"off",
                  mouseScrollReverse:"default",
                  onHoverStop:"off",
                  bullets: {
                    enable:true,
                    hide_onmobile:false,
                    style:"uranus",
                    hide_onleave:false,
                    direction:"horizontal",
                    h_align:"center",
                    v_align:"bottom",
                    h_offset:0,
                    v_offset:50,
                    space:5,
                    tmp:''
                  }
                },
                responsiveLevels:[1240,1024,778,480],
                visibilityLevels:[1240,1024,778,480],
                gridwidth:[1240,1024,778,480],
                gridheight:[868,768,960,720],
                lazyType:"none",
                shadow:0,
                spinner:"off",
                stopLoop:"off",
                stopAfterLoops:-1,
                stopAtSlide:-1,
                shuffle:"off",
                autoHeight:"off",
                fullScreenAutoWidth:"off",
                fullScreenAlignForce:"off",
                fullScreenOffsetContainer: "",
                fullScreenOffset: "0",
                hideThumbsOnMobile:"off",
                hideSliderAtLimit:0,
                hideCaptionAtLimit:0,
                hideAllCaptionAtLilmit:0,
                debugMode:false,
                fallbacks: {
                  simplifyAll:"off",
                  nextSlideOnWindowFocus:"off",
                  disableFocusListener:false,
                }
              });
              revapi27.bind("revolution.slide.onloaded",function (e) {
                revapi27.addClass("tiny_bullet_slider");
              });
            }

            if(revapi27) revapi27.revSliderSlicey();
          });   /*ready*/
        </script>-->

<?php else: ?>


<!--<script type="text/javascript">
          var tpj=jQuery;

          var revapi13;
          tpj(document).ready(function() {
            if(tpj("#rev_slider_13_1").revolution == undefined){
              revslider_showDoubleJqueryError("#rev_slider_13_1");
            }else{
              revapi13 = tpj("#rev_slider_13_1").show().revolution({
                sliderType:"standard",
                jsFileLocation:"/frontend/include/rs-plugin/js/",
                sliderLayout:"fullscreen",
                dottedOverlay:"none",
                delay:9000,
                particles: {startSlide: "first", endSlide: "last", zIndex: "1",
                  particles: {
                    number: {value: 80}, color: {value: "#000000"},
                    shape: {
                      type: "circle", stroke: {width: 0, color: "#FFF", opacity: 1},
                      image: {src: ""}
                    },
                    opacity: {value: 0.3, random: false, min: 0.25, anim: {enable: false, speed: 3, opacity_min: 0, sync: false}},
                    size: {value: 10, random: true, min: 1, anim: {enable: false, speed: 40, size_min: 1, sync: false}},
                    line_linked: {enable: true, distance: 200, color: "#000000", opacity: 0.2, width: 1},
                    move: {enable: true, speed: 3, direction: "none", random: true, min_speed: 3, straight: false, out_mode: "out"}},
                  interactivity: {
                    events: {onhover: {enable: true, mode: "bubble"}, onclick: {enable: false, mode: "repulse"}},
                    modes: {grab: {distance: 400, line_linked: {opacity: 0.5}}, bubble: {distance: 400, size: 150, opacity: 1}, repulse: {distance: 200}}
                  }
                },
                navigation: {
                  keyboardNavigation:"off",
                  keyboard_direction: "horizontal",
                  mouseScrollNavigation:"off",
                  mouseScrollReverse:"default",
                  onHoverStop:"off",
                  arrows: {
                    style:"gyges",
                    enable:true,
                    hide_onmobile:false,
                    hide_onleave:false,
                    tmp:'',
                    left: {
                      h_align:"center",
                      v_align:"bottom",
                      h_offset:-20,
                      v_offset:0
                    },
                    right: {
                      h_align:"center",
                      v_align:"bottom",
                      h_offset:20,
                      v_offset:0
                    }
                  }
                },
                responsiveLevels:[1240,1024,767,480],
                visibilityLevels:[1240,1024,767,480],
                gridwidth:[1240,1024,778,480],
                gridheight:[868,768,960,767],
                lazyType:"none",
                shadow:0,
                spinner:"off",
                stopLoop:"on",
                stopAfterLoops:0,
                stopAtSlide:1,
                shuffle:"off",
                autoHeight:"off",
                fullScreenAutoWidth:"off",
                fullScreenAlignForce:"off",
                fullScreenOffsetContainer: "",
                fullScreenOffset: "0",
                disableProgressBar:"on",
                hideThumbsOnMobile:"on",
                hideSliderAtLimit:0,
                hideCaptionAtLimit:0,
                hideAllCaptionAtLilmit:0,
                debugMode:false,
                fallbacks: {
                  simplifyAll:"off",
                  nextSlideOnWindowFocus:"off",
                  disableFocusListener:false,
                }
              });
            }

            RsParticlesAddOn(revapi13);
          });   /*ready*/
        </script>-->
<?php endif; ?>


@yield('scripts')


<script src="{{asset('/frontend/js/classie.min.js')}}"></script>
<script src="{{asset('/frontend/js/boxesFx.min.js')}}"></script>
{{--<script src="{{asset('/frontend/js/fourBoxSlider.js')}}"></script>--}}
<script src="{{asset('/frontend/js/fourBoxSlider.min.js')}}"></script>

<script type="text/javascript">

   <?php if (!$mobile->isMobile()): ?>
    $('div.bg-img img').attr('style', 'max-width:none !important;');
   <?php else: ?>
    $('div.bg-img img').attr('style', 'max-width:none !important;margin-left: -67.2vw;');
   <?php endif; ?>

    $('div.bg-img').attr('style', 'background:none !important;');
    $('div#boxgallery span.prev, div#boxgallery span.next').attr('style', 'z-index:10000;');

    $('div#blank_loader').fadeOut(500);

    setSliderAuto();
    setRandomSlide();

</script>
<script>
  AOS.init();
</script>

</body>
