$('#formCalcularReserva').submit(function (event) {
      event.preventDefault();
      var url = $(this).attr('action');
      var form_data = $(this).serialize();
      $.post(url, form_data, function (data) {
        $('#calcReserv_result').html(data).show();
      });
});


var loadDate = function(){
    var dateRangeObj = window.dateRangeObj;
    dateRangeObj.locale.format = 'DD MMM, YY';
    var start = new Date();
    start.setDate(start.getDate() + 1);
    var end = new Date();
    end.setDate(end.getDate() + 2);
    dateRangeObj.startDate = start;
    dateRangeObj.endDate = end;
    $(".daterange1").daterangepicker(dateRangeObj);
}
loadDate();
  /****************************************************************************/

  $('body').on('click', '.calc_createNew', function () {
      
    var form_data = $('#formCalcularReserva').serializeArray();
    var info = $(this).data('info'); 
      
    var data = {
      _token: window.csrf_token,
      form_data: form_data,
      info: info,
    };
    $.post('/admin/reservas/new', data, function (data) {
      $('#modalCalculateBook').modal('hide');
      $("#modalNewBook").modal();
      $('.contentNewBook').empty().append(data);
//      calculate(null, false);
    });
  });