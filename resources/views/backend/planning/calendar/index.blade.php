{!! $content!!}
<script type="text/javascript">


  var eventList = <?php echo json_encode($eventDatas) ?>;

 $(document).ready(function() {
  $('.content-calendar .tip').on('click','.calLink',function(event){
    location.href = $(this).data('href');
  });
  $('.content-calendar .tip').hover(function(event){
    var span = $(this).find('span');
    var eventText = eventList[span.data('id')];
    if (eventText){
      span.html(eventText);
    }
    
    if (screen.width<768){
      span.css('position','fixed');
      span.css('top','auto');
      span.css('bottom','-9px');
      span.css('left', 'auto');
      span.css('right', '3px');
//    } else {
//      span.css('top', (event.screenY-120));
//      span.css('left', (event.pageX-100));
//      console.log(event.screenY-120,event.pageX-100)
    }
  });
   });
</script>
<style>
  .calLink {
    background-color: #0173ff;
    color: #dadada;
    font-weight: bold;
    text-align: center;
  }
  
  .btn-fechas-calendar {
    color: #fff;
    background-color: #899098;
  }
  #btn-active {
    background-color: #10cfbd;
  }
  
      
  .content-calendar .tip:hover .end,
  .content-calendar .tip:hover .start,
  .content-calendar .tip:hover .total{
    background-color: red !important;
  }
  .content-calendar   a.tip:hover span {
    bottom: -47px;
    top: auto !important;
    cursor: default;
    white-space: nowrap;
  }
  
    .content-calendar .td-calendar{
    border:1px solid grey;width: 24px; height: 20px;
  }
  .content-calendar .no-event{
    border:1px solid grey;width: 24px; height: 20px;
  }
  .content-calendar .ev-doble{
    border:1px solid grey;width: 24px; height: 20px;
  }
  .content-calendar .start{
    width: 45%;float: right; cursor: pointer;
  }
  .content-calendar .end{
    width: 45%;float: left; cursor: pointer;
  }
  .content-calendar .total{
    width: 100%;height: 100%; cursor: pointer;max-height: 20px;
  }
  .content-calendar .td-month{
    border:1px solid black;width: 24px; height: 20px;font-size: 10px;padding: 5px!important;
    text-align: center;
    min-width: 25px;
  }

</style>