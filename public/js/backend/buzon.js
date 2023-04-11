
$(document).ready(function () {

  $('body').on('click', '.openSafetyBox', function (event) {
    var bID = $(this).data('id');
    $.ajax({
      url: '/ajax/showSafetyBox/' + bID,
      type: 'GET',
      success: function (response) {
        $('#modalSafetyBox_content').html(response);
        $('#modalSafetyBox_title').html('Enviar Recordatorio Buzón');
        $('#modalSafetyBox').modal('show');
      },
      error: function (response) {
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });

  });
  
  $('body').on('change', '#sasfeBoxSite', function (event) {
    var bID = $(this).val();
    var siteName = $( "#sasfeBoxSite option:selected" ).text();
//    console.log(bID);
    if (bID == 'undefined' || bID < 1) return;
    $.ajax({
      url: '/ajax/showSafetyBox-site/' + bID,
      type: 'GET',
      success: function (response) {
        $('#modalSafetyBox_content').html(response);
        $('#modalSafetyBox_title').html('Cajas de seguridad de '+siteName);
        $('#modalSafetyBox').modal('show');
      },
      error: function (response) {
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });
  });
  
  $('body').on('change', '.editSafeBox', function (event) {
    var value = $(this).val();
    var id = $(this).data('id');
    var field = $(this).data('field');
    $.ajax({
      url: '/ajax/showSafetyBox-updKey',
      type: 'POST',
      data:{id:id,val:value,field:field},
      success: function (response) {
        if (response == 'OK') {
          
          window.show_notif('','success','Caja de seguridad guardada.');
        } else {
          window.show_notif('','error',response);
        }
      },
      error: function (response) {
        window.show_notif('','error',response);
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });

  });
  $('body').on('click', '#createSafetyBox', function (event) {
    var caja = $('#safetyBox_caja').val();
    var color = $('#safetyBox_color').val();
    var clave = $('#safetyBox_clave').val();
    var roomID = $('#safetyBox_room').val();
    var site = $('#safetyBox_site').val();
    var roomName = $( "#safetyBox_room option:selected" ).text();
    $.ajax({
      url: '/ajax/createSafetyBox',
      type: 'POST',
      data:{caja:caja,color:color,clave:clave,site:site,room_id:roomID},
      success: function (response) {
        if (response == 'OK') {
          window.show_notif('','success','Caja generada');
          $('#safetyBox_clave').val('');
          $("#modalSafetyBox_content").find('tbody')
          .append($('<tr>')
              .append($('<td>').text(color+' - '+caja))
              .append($('<td>').text(roomName))
              .append($('<td>').text(clave))
          );
        } else {
          window.show_notif('','error',response);
        }
      },
      error: function (response) {
        window.show_notif('','error',response);
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });

  });

  $('body').on('change', '#change_CajaAsig', function (event) {
    var value = $(this).val();
    var book = $(this).data('id');
    $.ajax({
      url: '/ajax/updSafetyBox/' + book + '/' + value,
      type: 'GET',
      success: function (response) {
        if (response == 'overlap'){
          window.show_notif('Caja ocupada:','danger','No se ha podido cambiar la Caja de seguridad');
        } else {
          window.show_notif('Caja Cargada:','success','Caja de seguridad asignada a la reserva');
          $('#modalSafetyBox_content').html(response);
        }
          
      },
      error: function (response) {
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });
  });
  
  $('body').on('change', '.upd_CajaAsig_lst', function (event) {
    var value = $(this).val();
    var book = $(this).data('id');
    $.ajax({
      url: '/ajax/updSafetyBox/' + book + '/' + value+'/1',
      type: 'GET',
      success: function (response) {
        if (response == 'ok'){
          window.show_notif('OK','success','Caja de seguridad cambiada');
        } else {
          if (response == 'overlap') 
            window.show_notif('Caja ocupada:','danger','No se ha podido cambiar la Caja de seguridad');
          else
            window.show_notif('Error:','danger','No se ha podido cambiar la Caja de seguridad');
        }
      },
      error: function (response) {
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });
  });


  $('#table_partee').on('click', '.msgs', function () {
    $('#conteiner_msg_lst').show();
    $('#box_msg_lst').html($(this).data('msg'))
//    console.log(msg);
  });
  $('#box_msg_close').on('click', function () {
    $('#conteiner_msg_lst').hide();
  });

  $('body').on('click', '.sendBuzonSMS', function (event) {
    var id = $(this).data('id');
    var that = $(this);
    if (that.hasClass('disabled-error')) {
      alert('Partee error.');
      return;
    }
    if (that.hasClass('disabled')) {
      return;
    }
    $('#loadigPage').show('slow');
    that.addClass('disabled')
    $.post('/ajax/send-SafetyBox-sms', {_token: $('#buzon_csrf_token').val(), id: id}, function (data) {
      if (data.status == 'danger') {
        window.show_notif('Buzón Error:', data.status, data.response);
      } else {
        window.show_notif('Buzón:', data.status, data.response);
        that.prop('disabled', true);
      }
      $('#loadigPage').hide('slow');
    });
  });
  $('body').on('click', '.sendBuzonMail', function (event) {
    var id = $(this).data('id');
    var that = $(this);
    if (that.hasClass('disabled-error')) {
      alert('Partee error.');
      return;
    }
    if (that.hasClass('disabled')) {
//          alert('No se puede enviar el SMS.');
      return;
    }
    $('#loadigPage').show('slow');
    that.addClass('disabled')
    $.post('/ajax/send-SafetyBox-mail', {_token: $('#buzon_csrf_token').val(), id: id}, function (data) {
      if (data.status == 'danger') {
        window.show_notif('Buzón Error:', data.status, data.response);
      } else {
        window.show_notif('Buzón:', data.status, data.response);
        that.prop('disabled', true);
      }
      $('#loadigPage').hide('slow');
    });
  });



  /**
 *  Recordatorio de pago
 */

$('body').on('click','#btnRememberSecPayment',function(event) {
  $('#modalRememberSecPayment').modal('show');
  var bID = $(this).data('id');
  $('#_btnRememberSecPaymentBox').empty().load('/ajax/getPaymentRemainder/' + bID);
});
    
$('body').on('click', '.sendPaymentRemember', function (event) {
var bID = $(this).data('id');
$.ajax({
  url: '/ajax/sendPaymentRemainder/' + bID,
  type: 'GET',
  success: function (response) {
    window.show_notif('', response.status, response.response);

  },
  error: function (response) {
    alert('No se ha podido obtener los detalles de la consulta.');
  }
});
});

$('body').on('click', '.copyMsgRemeberPayment', function (event) {
  var data = $(this).data('msg');
  var dummy = document.createElement("textarea");
  $('#copyMsgRemeberPayment').append(dummy);
  //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
  dummy.value = data;
  dummy.select();
  document.execCommand("copy");
  $('#copyMsgRemeberPayment').html('');

  alert('Mensaje Copiado');
});


});



var copyBuzonMsg = function (bookID, elem = null, tooltip = 'tooltipPartee') {
  $.get('/ajax/SafetyBoxMsg/' + bookID,
          function (data) {
            if (data == 'empty') {
              alert('No se ha encontrado un registro asociado');
            } else {
              var dummy = document.createElement("textarea");
              // to avoid breaking orgain page when copying more words
              // cant copy when adding below this code
              // dummy.style.display = 'none'
              if (elem) {
                $('#' + elem).append(dummy);
              } else {
                document.body.appendChild(dummy);
              }
              //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
              dummy.value = data;
              dummy.select();
              document.execCommand("copy");
              if (elem) {
                $('#' + elem).html('');
              } else {
                document.body.removeChild(dummy);
              }
              if (tooltip) {
                $('#' + tooltip).addClass('show');
                setTimeout(function () {
                  $('#' + tooltip).removeClass('show');
                }, 5000);
              } else {
                alert('Mensaje Copiado');
              }
            }

          });
}