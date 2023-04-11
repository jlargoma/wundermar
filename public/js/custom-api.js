$(function () {
  $("#dates").daterangepicker({
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

$('.back-to-form').click(function () {
  $('#content-info').hide();
  $('#content-form').show();
});

$('#api-form').submit(function (event) {
  event.preventDefault();
  var url = $(this).attr('action');
  var result = { };
  $.each($('#api-form').serializeArray(), function() {
    result[this.name] = this.value;
  });

  $.post(url, {result : result}).done(function (data) {
    //$('#content-form').hide();
    $('#content-info').empty().append(data);
    $('#content-info').show();
  });
});
