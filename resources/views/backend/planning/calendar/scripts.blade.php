<script type="text/javascript">
  $(document).ready(function () {
    window["cal_move"] = false;

    var hideCalendarSite = function () {
      var siteID = $('#cal_site_id').val();
      
      if (siteID > 0) {
        var classFilter = 'site' + siteID;
        $('.contentCalendar tbody tr').each(function (v, i) {
          if ($(this).hasClass(classFilter)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }
    }

    var moveCalendar = function () {
      if (window.cal_move)
        return;
      window.cal_move = true;
      $('.btn-fechas-calendar').css({
        'background-color': '#899098',
        'color': '#fff'
      });
      $('#btn-active').css({
        'background-color': '#10cfbd',
        'color': '#fff'
      });
      var target = $('#btn-active').attr('data-month');
      var targetPosition = $('.contentCalendar #month-' + target).position();
      $('.contentCalendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
      hideCalendarSite();
    }



    window["moveCalendar"] = moveCalendar;
//    setTimeout(function () { moveCalendar();},500);
//   $('#btn-active').trigger('click');

    $('.content-calendar').on('click', '.reloadCalend', function (Event) {
      var time = $(this).attr('data-time');
      window.cal_move = false;
      $('.content-calendar').empty().load(
              window.URLCalendar + time,
              function () {
                moveCalendar();
              }
      );
      Event.stopPropagation()
    });



    // Ver imagenes por piso

    $('body').on('click','.getImages',function (event) {
      var idRoom = $(this).attr('data-id');
      $.get('/admin/rooms/api/getImagesRoom/' + idRoom, function (data) {
        $('#modalRoomImages .modal-content').empty().append(data);
      });
    });

    // Cargamos el calendario cuando acaba de cargar la pagina
    setTimeout(function () {
      $('.content-calendar').empty().load('/getCalendarMobile',
              function () {
                moveCalendar();
              });
    }, 1500);

  });


</script>

