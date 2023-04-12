<?php   
$isMobile = $mobile;
?>
<div class="row">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-close fs-20" style="color: #000!important;"></i>
    </button>
</div>

<div class="col-md-12 not-padding content-last-books">
    <div class="alert alert-info fade in alert-dismissable" style="background-color: #daeffd!important;">
        <h4 class="text-center">ALARMAS DE BAJO BENEFICIOS</h4>
        <div class="table-responsive">
        <table class="table" >
          <thead >
            @if($isMobile)
              <th class="text-center bg-complete text-white static" style="width: 130px; padding: 14px !important;">  
            @else
              <th class="text-left bg-complete text-white" style="width: 25%;" >
            @endif
                      Nombre</th>
            @if($isMobile)
              <th class ="text-center bg-complete text-white first-col" style="padding-left: 130px!important">
            @else
              <th class="text-center bg-complete text-white" style="width: 5%;" >
            @endif
             Apto</th>
                <th class ="text-center bg-complete text-white" style="width: 17% !important;font-size:10px!important">IN - OUT</th>
                <th class ="text-center bg-complete text-white" style="width: 16% !important;font-size:10px!important">
                  PVP<br/><b id="alarms_totalPVP"></b></th>
                <th class ="text-center bg-complete text-white" style="width: 17% !important;font-size:10px!important">
                  Coste Total<br/><b id="alarms_totalCosteTotal"></b></th>
                <th class ="text-center bg-complete text-white" style="width: 10% !important;font-size:10px!important">%Benef</th>
                <th class ="text-center bg-complete text-white" style="width: 10% !important;font-size:10px!important"></th>
            </thead>
            <tbody id="low_profit_data">
            </tbody>
        </table>
        </div>
        <button id="activateAlertLowProfits" class="btn btn-xs btn-default " type="button" >
          <i class="fa fa-bell" aria-hidden="true"></i>&nbsp;Activar para todos
        </button>
        <button class="btn btn-xs btn-default " type="button" onclick="location.reload();">
          Actualizar los datos
        </button>
    </div>
</div> 


<script type="text/javascript">
    $(document).ready(function() {
      
      
      
      $('#low_profit_data').on('click','.toggleAlertLowProfits',function(event) {
      var id = $(this).attr('data-id');
      var objectIcon = $(this).find('i');
      var totalCount = $("#btnLowProfits").find('.numPaymentLastBooks');
      $.get('/admin/reservas/api/toggleAlertLowProfits', { id:id }, function(data) {
                    if (data.status == 'danger') {
                      window.show_notif(data.title,data.status,data.response);
                    } else {
                      var currentCount = totalCount.data('val');
                      
                      if (objectIcon.hasClass('fa-bell-slash')){
                        objectIcon.removeClass('fa-bell-slash').addClass('fa-bell');
                        currentCount++;
                      } else {
                        currentCount--;
                        objectIcon.removeClass('fa-bell').addClass('fa-bell-slash');
                      }
                      totalCount.data('val',currentCount)
                      totalCount.text(currentCount)
                      window.show_notif(data.title,data.status,data.response);
                        /**Change button alert class */
                        if ($('#list_lowProf').find('.fa-bell').length>0){
                          //hasn't active items
                          if ( !$('#btnLowProfits').hasClass('btn-alarms') )
                            $('#btnLowProfits').addClass('btn-alarms')
                        } else {
                          if ( $('#btnLowProfits').hasClass('btn-alarms') )
                            $('#btnLowProfits').removeClass('btn-alarms')
                        }
                        
                    }
                });
         
            
        });
         
    $('#activateAlertLowProfits').click(function(event) {
    
    if (confirm("Esto activará las alarmas de todos los registros. Desea continuar?")){
      
      var id = $(this).attr('data-id');
      var objectIcon = $(this).find('i');
      $.get('/admin/reservas/api/activateAlertLowProfits', function(data) {
                    if (data.status == 'danger') {
                        window.show_notif(data.title,data.status,data.response);
                        location.reload();
                    } else {
                        window.show_notif(data.title,data.status,data.response);
                        location.reload();
                    }
                });
         
        }    
        });
        
    });
</script>