
$(document).ready(function () {
/***************************************************************************/
//encuesta
  var getEncuestaModal = function(bID){
     $.ajax({
      url: '/admin/showFormEncuesta/' + bID,
      type: 'GET',
      success: function (response) {
        $('#modalSendPartee_content').html(response);
        $('#modalSendPartee_title').html('Envío de Encuestas');
        $('#modalSendPartee').modal('show');
      },
      error: function (response) {
        alert('No se ha podido obtener los detalles de la consulta.');
      }
    });
  }
    
  $('body').on('click', '.open_modal_encuesta', function (event) {
    var bID = $(this).data('id');
    getEncuestaModal(bID);
  });
  $('body').on('click', '.form_sendEncuesta', function (event) {
    var id = $(this).data('id');
    var action = $(this).data('action');
    var that = $(this);
    $('#loadigPage').show('slow');
     $.post('/admin/sendEncuesta', {_token: window.csrf_token, id: id,action:action}, function (data) {
      if (data.status == 'OK') {
        window.show_notif('OK', 'success', data.msg);
        getEncuestaModal(id);
      } else {
        window.show_notif('Error', 'danger', data.msg);
      }
      $('#loadigPage').hide('slow');
    });
  });
/***************************************************************************/
//INVOICE
 $('#updateBooking').on('click', '#open_invoice', function (event) {
    var bID = $(this).data('id');
    $('#loadigPage').show('slow');
    $('#modalSafetyBox').modal('show');
    $('#modalSafetyBox_title').text('Facturas');
    $('#modalSafetyBox_content').empty().load('/admin/facturas/modal/editar/'+bID);
    $('#loadigPage').hide('slow');
  });
  
  $('#modalSafetyBox').on('submit','#sendInvoiceBook',function (e){
      e.preventDefault();
      e.stopPropagation();
      $('#modalSafetyBox_content').empty();  
      $('#loadigPage').show('slow');
       $.ajax({
        url: $(this).attr('action'),
        type: 'post',
        data: $(this).serialize(),
        success: function(data) {
          $('#modalSafetyBox_title').text('Confirmar Factura');
          $('#modalSafetyBox_content').html(data);
          $('#loadigPage').hide('slow');
          }
      });
    
    });
    
    $('#modalSafetyBox').on('click','#backEditInvoice',function (e){
      e.preventDefault();
      e.stopPropagation();
      $('#loadigPage').show('slow');
      $('#modalSafetyBox').modal('show');
      $('#modalSafetyBox_title').text('Facturas');
      $('#modalSafetyBox_content').empty().load('/admin/facturas/modal/editar/'+$(this).data('book_id'));
      $('#loadigPage').hide('slow');
    
    });
    
    $('#modalSafetyBox').on('click','#sendInvoiceEmail',function (e){
      e.preventDefault();
      e.stopPropagation();
      if(confirm('Enviar factura a '+ $(this).data('email') +'?')){
        $('#loadigPage').show('slow');
         $.ajax({
          url: '/admin/facturas/enviar',
          type: 'POST',
          data: {
            id: $(this).data('id'),
            _token: window.csrf_token
          },
          success: function(data) {
            $('#modalSafetyBox_title').text('Enviar Factura');
            $('#modalSafetyBox_content').html(data);
            $('#loadigPage').hide('slow');
            }
        });
      }
    });
    
/***************************************************************************/
//LEADs

  $('#updateBooking').on('click', '#open_CustomerRequest', function (event) {
    var bID = $(this).data('id');
    $('#loadigPage').show('slow');
    $('#modalSafetyBox').modal('show');
    $('#modalSafetyBox_title').text('LEADs');
    $('#modalSafetyBox_content').empty().load('/admin/getCustomerRequestBook/'+bID);
    $('#loadigPage').hide('slow');
  });
 
    $('#updateBooking').on('click','#saveCustomerRequest',function(){
    var id = $(this).data('id');
    var user_id = $('#CRE_user').val();
    if (user_id=="" ){
      alert('El usuario es requerido');
      return;
    }
    var data = {
      _token: window.csrf_token,
      id: id,
      userID: $('#CRE_user').val(),
      comments: $('#CRE_comment').val(),
      send_mail: 0,
    };
    
    $.post('/admin/saveCustomerRequest', data, function (response) {
      if (response == 'OK'){
        window.show_notif('','success', 'Item guardado.');
        location.reload();
      } else {
        if (response == 'errorMail')
          window.show_notif('','error', 'No se pudo enviar el registro.');
        else  window.show_notif('','error', 'No se pudo guardar el registro.');
      }
    });
  });
  
  $('body').on('click','#consultGH',function(){
    var configuracion_ventana = "resizable=yes,scrollbars=yes,status=yes";
    window.open("/admin/consultar-google",'_blank',configuracion_ventana);
  });
});