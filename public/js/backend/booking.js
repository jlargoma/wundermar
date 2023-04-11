/***
 * JS Update Bookings
 */
$(document).ready(function() {
    let dateRangeObj = Object.assign({}, window.dateRangeObj);
    dateRangeObj.locale.format = 'DD MMM, YY';
    $(".daterange1").daterangepicker(dateRangeObj);
    var newPvp = 0;
    var newDisc = null;
    var newPromo = null;
    /**   */
    function getDatesBooking( data, override = true ) {
      if (typeof $('.daterange02').val() == "undefined") var date = $('.daterange1').val();
        else var date       = $('.daterange02').val();

      var arrayDates = date.split('-');
      var res1       = arrayDates[0].replace("Abr", "Apr");
      var date1      = new Date(res1);
      var res2       = arrayDates[1].replace("Abr", "Apr");
      var date2      = new Date(res2);
      
      var timeDiff   = Math.abs(date2.getTime() - date1.getTime());
      var nigths     = Math.ceil(timeDiff / (1000 * 3600 * 24));
      
      return {'start':date1.yyyymmmdd(),'finish':date2.yyyymmmdd(),'nigths': nigths};
    }
    /***************************/
    function fixedPrices(status){
      var status     = $('select[name=status]').val();
      var sizeApto   = $('select[name=newroom] option:selected').attr('data-size');
      if ( status == 8) {
        $('.total').empty().val(0);
        $('.cost').empty().val(0);
        $('.beneficio').empty().val(0);
        return true;
      }
      if ( status == 7) {
        if (sizeApto == 1) {
          $('.total').empty().val(30);
          $('.cost').empty().val(30);
          $('.beneficio').empty().val(0);
          return true;
        }

        if (sizeApto == 3 || sizeApto == 4){
          $('.total').empty().val(100);
          $('.cost').empty().val(70);
          $('.beneficio').empty().val(30);
          return true;
        }

        $('.total').empty().val(50);
        $('.cost').empty().val(40);
        $('.beneficio').empty().val(10);
        return true;
      }
      return false;
    }
    
    /********************************************/
    function sendCalculatePrices(){
      var start_date  = $('#start').val();
      var finish_date  = $('#finish').val();
      var nigths  = $('.nigths').val();
      var room  = $('#newroom').val();
      var pax   = $('.pax').val();
      var park  = $('.parking').val();
      var lujo  = $('select[name=type_luxury]').val();
        
      if ( room == "" || pax == "") return null;
        
      $('.loading-div').show();
      var auxTotal = $('.total').val();
      var auxCosteApto = parseInt($('.costApto').val());
      var auxCoste = parseInt($('.cost').val());
      var agencyCost = $('.agencia').val();
      var promotion = $('.promociones').val();
      var agencyType = $('.agency').val();
      var totalPrice = $('.total').val();
      var totalCost = $('.cost').val();
      var apartmentCost = $('.costApto').val();
      var parkingCost = $('.costParking').val();
      var book_id = $('#bkgID').val();

      $.get('/admin/api/reservas/getDataBook', {
          start: start_date,
          finish: finish_date,
          pax: pax,
          room: room,
          park: park,
          lujo: lujo,
          agencyCost: agencyCost,
          promotion: promotion,
          agencyType: agencyType,
          book_id: book_id
      }).done(function( data ) {
        if (!data) return null;
        
            $('#computed-data').html(JSON.stringify(data));
            $('#minDay').val(data.aux.minDay).removeClass('danger');
            if (nigths<data.aux.minDay){
              $('#minDay').addClass('danger');
            }

        if (data.public.promo_pvp<1) {
//              $('.promociones').val('');
//              $('.book_owned_comments').empty();
              $('.content_image_offert').hide();
          } else {
              $('.promociones').val(data.public.promo_pvp);
              $('.book_owned_comments').html('('+data.public.promo_name+' : '+ Math.abs(data.public.promo_pvp) +' €)');
              $('.content_image_offert').show();
          }
          
          newPvp = data.calculated.total_price;
          if (data.public.discount_pvp>0)
            newDisc = '<b>Descuento del '+data.public.discount+'%:</b> -'+window.formatterEuro.format(data.public.discount_pvp)+'';
          else newDisc = null;
          if (data.public.promo_pvp>0)
            newPromo = '<b>Promo '+data.public.promo_name+':</b> -'+window.formatterEuro.format(data.public.promo_pvp)+''
          else newPromo = null;
          
          $('#total_pvp').val(data.calculated.total_price);
          $('.cost').val(data.calculated.total_cost);
          $('.costApto').val(data.costes.book);
          $('.costParking').val(data.costes.parking);
          $('.beneficio').val(data.calculated.profit);
          $('.beneficio-text').html(data.calculated.profit_percentage + '%');
          $('#real-price').html(data.calculated.real_price);
          $('#publ_price').html(data.public.pvp_init);
          $('#publ_disc').html(data.public.discount_pvp);
          $('#publ_promo').html(data.public.promo_pvp);
          $('#publ_limp').html(data.public.price_limp);
          $('#publ_total').html(data.public.pvp);
      });
      $('.loading-div').hide();
    }
    
    
    /**     */
    function calculate( data, override = true ) {
        var comentario = $('.book_comments').val();
        if ( override ){
	  if (fixedPrices()){
            return null;
          }
        }
       sendCalculatePrices();
    };




    $('.daterange1').change(function(event) {
        var date = $(this).val();
        var aDate = date.trim().split(' - ');
        var start = new Date(aDate[0]);
        var end = new Date(aDate[1]);

        var timeDiff = Math.abs(start.getTime() - end.getTime());
        var diffDays = Math.ceil(timeDiff / (1090 * 3600 * 24));
        $('.nigths').val(diffDays);
        $(this).closest('.input_dates').find('.date_start').val(start.yyyymmmdd());
        $(this).closest('.input_dates').find('.date_finish').val(end.yyyymmmdd());

        calculate();

    });

    $('#newroom').change(function(event){

      var room = $('#newroom').val();
      var pax  = parseFloat($('.pax').val());
      if ( room != "" && pax != "") {
        $.get('/admin/apartamentos/getPaxPerRooms/'+room).done(function( data ){
          if (pax < data) {
              $('.personas-antiguo').empty().append('Van menos personas que el mínimo, se cobrará el mínimo de personas que son :'+data);
          }else{
              $('.personas-antiguo').empty();
          }
        });
      }

      var dataLuxury = $('option:selected', this).attr('data-luxury');
      if (dataLuxury == 1) {
          $('.type_luxury option[value=1]').attr('selected','selected');
          $('.type_luxury option[value=2]').removeAttr('selected');
      } else {
          $('.type_luxury option[value=1]').removeAttr('selected');
          $('.type_luxury option[value=2]').attr('selected','selected');
      }
      calculate();
    });

    $('.pax').change(function(event){

      var room     = $('#newroom').val();
      var real_pax = $('.real_pax').val();
      var pax      = parseInt( $('.pax').val() );

      $('.real_pax option').each(function(index, el) {
          $(this).attr('selected',false);
      });
      $('.real_pax option[value='+pax+']').attr('selected','selected');

            if (room != "") {
                $.get('/admin/apartamentos/getPaxPerRooms/'+room).done(function( data ){
                    if (pax < data) {
                        $('.personas-antiguo').empty();
                        $('.personas-antiguo').append('Van menos personas que el mínimo, se cobrará el mínimo de personas que son :'+data);
                    }else{
                        $('.personas-antiguo').empty();
                    }
                });
            }
            calculate();
        });

        $('.recalc').change(function(event){
            calculate();
        });

        $('.total').focus(function(event) {
            // If this doesn't have main data attached
            if ($(this).attr('old-data') === undefined) {
                // We attach it to know that has been modified
                $(this).attr('old-data', $(this).val());
            }
        });
        // Total Cost Calc
        $('.total, .cost').change(function(event) {
            calculateProfit();
        });
        $('.total').change(function(event) {
            calculateProfit();
        });

        // Coste Apto
        $('.costApto').focus(function(event) {
            $(this).attr('data-cost-on-focus', $(this).val());
        });
        $('.costApto').change(function(event) {
            var oldValue = ($(this).attr('data-cost-on-focus'));
            var diff = oldValue - $(this).val();

            var totalCost = parseFloat($('.cost').val() - diff).toFixed(2);
            $('.cost').val(totalCost);

            $(this).attr('data-cost-on-focus', $(this).val());
            calculateProfit();
        });
        // Coste Parking
        $('.costParking').focus(function(event) {
            $(this).attr('data-cost-on-focus', $(this).val());
        });
        $('.costParking').change(function(event) {
            var oldValue = ($(this).attr('data-cost-on-focus'));
            var diff = oldValue - $(this).val();

            var totalCost = parseFloat($('.cost').val() - diff).toFixed(2);
            $('.cost').val(totalCost);

            $(this).attr('data-cost-on-focus', $(this).val());
            calculateProfit();
        });

        function calculateProfit() {
            var pvp = $('#total_pvp').val();
            var totalCost = $('.cost').val();

            var profit = (parseFloat(pvp) - parseFloat(totalCost)).toFixed(2);
            var profitPercentage = Math.round((profit / pvp) * 100);
            $('.beneficio').val(profit);
            $('.beneficio-text').html(profitPercentage + ' %');
        }

        // Reset Changes
        $('#reset').click(function() {
            calculate();

            var $el = $(this);
            $el.addClass('fa-spin');

            $('.loading-div').show();

            setTimeout(function() {
                $el.removeClass('fa-spin');
                $('.loading-div').hide();
            }, 1000);
        });


        $('.country').change(function(event) {
            var value = $(this).val();
            if ( value != 'ES') {
                $('.content-cities').hide();
            } else {
                $('.content-cities').show();

            }
        });
        
        
        
        
        if ($('#update_php').val() == 1){ // Datepicker and more for update book?>


            $('#payments_block').on('click','.cobrar',function (event) {
                var id = $(this).attr('data-id');
                var date = $('.fecha-cobro').val();
                var importe = $('.importe').val();
                var comment = $('.comment').val();
                var type = $('.type_payment').val();
                if (importe == 0) {

                } else {
                  $.get('/admin/pagos/create', {id: id, date: date, importe: importe, comment: comment, type: type}).success(function (data) {
                    window.location.reload();
                  });
                }
            });


            $('#payments_block').on('change','.editable',function (event) {
                var id = $(this).attr('data-id');
                var importe = $(this).val();
                $.get('/admin/pagos/update', {id: id, importe: importe}, function (data) {
                  window.location.reload();
                });
            });



            $('.cliente').change(function(event) {
                var data = {
                    id: $('#customer_id').val(),
                    name: $('#c_name').val(),
                    email: $('#c_email').val(),
                    phone: $('#c_phone').val(),
                    address: $('#c_address').val(),
                    dni: $('#c_dni').val(),
                    _token: window.csrf_token
                };
                $.post('/admin/clientes/save', data, function(data) {
                        $('.notification-message').val(data);
                        document.getElementById("boton").click();
                        setTimeout(function(){
                            $('.pgn-wrapper .pgn .alert .close').trigger('click');
                             }, 1000);
                });
            });

            $('#overlay').hover(function() {
                $('.guardar').show();
            }, function() {
                $('.guardar').hide();
            });

            $('.status').change(function(event) {
                $('.content-alert-success').hide();
                $('.content-alert-error1').hide();
                $('.content-alert-error2').hide();
                var status = $(this).val();
                var id     = $(this).attr('data-id');
                var clase  = $(this).attr('class');
                var email = $('input[name=email]').val();

                if (email == "") {
                    $('.guardar').emtpy;

                    $('.guardar').text("Usuario sin e-mail");
                    $('.guardar').show();
                }

                if (status == 5) {
                    $('#modalStatusContestado').find('.contentEmailing').attr('src','/admin/reservas/ansbyemail/' + id);
                    $('#modalStatusContestado').modal('show');
//                    $('#contentEmailing').empty().load('/admin/reservas/ansbyemail/'+id);
//                    $('#btnEmailing').trigger('click');


                }else{
                    $.get('/admin/reservas/changeStatusBook/'+id, { status:status }, function(data) {
                        window.show_notif(data.title,data.status,data.response);
                    });
               }

            });


            $('.confirm_PVP_send').click(function(event) {
              var type = 1;
              if ($(this).attr('id') == 'cpvps_refuse') type = 0;
              sendFormBooking($(this).data('value'),type);
            });
            $('#updateForm').submit(function(event) {
                event.preventDefault();
                newPvp = parseFloat(newPvp);
                var totalPvp = parseFloat($('input[name="total"]').val());
                 
                if (newPvp == 0){
                  sendFormBooking(totalPvp,0);
                  return;
                }
               
                if (newPvp == totalPvp){
                  sendFormBooking(totalPvp,0);
                  return;
                }
                $('#confirm_PVP_current').html( window.formatterEuro.format(totalPvp));
                $('#confirm_PVP_modif').html(window.formatterEuro.format(newPvp));
                $('#confirm_PVP_disc').html(newDisc);
                $('#confirm_PVP_promo').html(newPromo);
                
                $('#modal_confirm_PVP').modal();
                $('#cpvps_acept').data('value',newPvp);
                $('#cpvps_refuse').data('value',totalPvp);
                return false;
              });
              
              
    function sendFormBooking(totalPvp,updMetaPrice=0){
      $('.loading-div').show();
      var data = {
          _token : $('input[name="_token"]').val(),
          nombre : $('input[name="nombre"]').val(),
          email  : $('input[name="email"]').val(),
          phone  : $('input[name="phone"]').val(),
          dni      : $('input[name="dni"]').val(),
          address  :  $('input[name="address"]').val(),
          country  : $('select[name="country"]').val(),
          province : $('select[name="province"]').val(),
          fechas   : $('input[name="fechas"]').val(),
          nigths   : $('input[name="nigths"]').val(),
          pax      : $('select[name="pax"]').val(),
          real_pax : $('select[name="real_pax"]').val(),
          newroom  : $('select[name="newroom"]').val(),
          parking  : $('select[name="parking"]').val(),
          type_luxury : $('select[name="type_luxury"]').val(),
          schedule    : $('select[name="schedule"]').val(),
          scheduleOut : $('select[name="scheduleOut"]').val(),
          agency      : $('select[name="agency"]').val(),
          agencia     : $('input[name="agencia"]').val(),
          promociones : $('.promociones').val(),
          total_pvp   : totalPvp,
          cost        : $('input[name="cost"]').val(),
          costApto    : $('input[name="costApto"]').val(),
          costParking : $('input[name="costParking"]').val(),
          beneficio   : $('input[name="beneficio"]').val(),
          start       : $('#start').val(),
          finish      : $('#finish').val(),
          comments    : $('textarea[name="comments"]').val(),
          computed_data : $('#computed-data').html(),
          book_comments : $('textarea[name="book_comments"]').val(),
          customer_id   : $('input[name="customer_id"]').val(),
          book_owned_comments  : $('textarea[name="book_owned_comments"]').val(),
          updMetaPrice  : updMetaPrice
          }
          var url = $('#updateForm').attr('action');
          $.post( url ,data,
          function(data) {
              window.show_notif(data.title,data.status,data.response);
              if (data.status == "success") {
                  location.reload();
              }
              $('.loading-div').hide();
          });
      };

      $('textarea[name="comments"],textarea[name="book_comments"], textarea[name="book_owned_comments"]').change(function(event) {

          var value = $(this).val();
          var type = $(this).attr('data-type');
          var book = $(this).attr('data-idBook');

          $.get('/admin/books/'+book+'/comments/'+type+'/save', { value: value }, function(data) {
            window.show_notif(data.title,data.status,data.response);

              
          });

      });
      
        setTimeout(function () {
//          calculate(null, false);
          window.getPayments();}, 150);
    }

    $('.loading-div').hide();
        
    if ($('#new_book').val() == 1)  
      setTimeout(function () { calculate(null, false);}, 250);
  
    $('#sendShareImagesEmail').click(function (event) {
        if (confirm('¿Quieres reenviar las imagenes')) {
          var email = $('#shareEmailImages').val();
          var register = $('#registerData').val();
          var roomId = $('#newroom').val();
          $.get('/admin/sendImagesRoomEmail', {email: email, roomId: roomId, register: register, returned: '1'},
                function(data) {location.reload();});
        }
    });
    
    $('#updateBooking').on('click','.cliHas',function(){
        var that = $(this);
        var type = that.data('t');
        var text = '';
        var data = {
          _token: window.csrf_token,
          bid: that.data('id'),
          type: type
        };
        $.post('/ajax/toggleCliHas', data, function (resp) {
          if (resp.status == 'OK'){
            window.show_notif('','success', 'Item guardado.');
            if (resp.result){
                switch(type){
                    case 'photos':
                        text = 'Fotos enviadas al cliente';
                        break;
                    case 'beds':
                        text = 'CON CAMAS SUPLETORIAS';
                        break;
                }
                that.addClass('active').attr('title',text);
            }
            else{ 
                switch(type){
                    case 'photos':
                        text = 'Fotos NO enviadas al cliente';
                        break;
                    case 'beds':
                        text = 'SIN CAMAS SUPLETORIAS';
                        break;
                }
                that.removeClass('active').attr('title',text);
            }


          } else {
            window.show_notif('','error', 'No se pudo guardar el registro.');
          }
        });
    });
 });