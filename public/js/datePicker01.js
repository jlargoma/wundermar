$(document).ready(function () {
window['dateRangeObj'] = {
    "buttonClasses": "button button-rounded button-mini nomargin",
    "applyClass": "button-color",
    "cancelClass": "button-light",
    autoUpdateInput: true,
    locale: {
      firstDay: 1,
      format: 'DD/MM/YYYY',
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
    },

  };
$(".daterange01").daterangepicker(window.dateRangeObj);
window.dateRangeObj.locale.format = 'DD MMM, YY';
$(".daterange02").daterangepicker(window.dateRangeObj);
window.dateRangeObj.locale.format = 'DD/MM/YYYY';

  Date.prototype.ddmmmyyyy = function () {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();
    return [
      (dd > 9 ? '' : '0') + dd,
      (mm > 9 ? '' : '0') + mm,
      this.getFullYear()
    ].join('/');
  };
  Date.prototype.yyyymmmdd = function () {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();
    return [
      this.getFullYear(),
      (mm > 9 ? '' : '0') + mm,
      (dd > 9 ? '' : '0') + dd
    ].join('-');
  };
  var render_yyyymmmdd = function (dates) {
    var date = dates.trim().split('/');
    return date[2] + '-' + date[1] + '-' + date[0];
  };
  
  Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
  }
});

$('.daterange1').on('change', function (event) {
    var date = $(this).val();

    var arrayDates = date.split('-');
    var res1 = arrayDates[0].replace("Abr", "Apr");
    var date1 = new Date(res1);
    var start = date1.getTime();
    var res2 = arrayDates[1].replace("Abr", "Apr");
    var date2 = new Date(res2);

    var content = $(this).closest('div');
    var startContent = content.find('.date_start');
    if (startContent)
        startContent.val(date1.yyyymmmdd());
    var endContent = content.find('.date_finish');
    if (endContent)
        endContent.val(date2.yyyymmmdd());


    var nigthContent = $(this).closest('form').find('.nigths');
    if (nigthContent) {
        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
        nigthContent.val(Math.round(timeDiff / (1000 * 3600 * 24)));
    }
});