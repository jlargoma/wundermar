<script type="text/javascript" src="{{asset('/js/bootbox.min.js')}}"></script>
<script type="text/javascript">

  $(document).ready(function () {

function toFixed(n,length){
            
            if(n % 1 != 0){
                return n.toFixed(length);
            }else{
                return n;
            }
            
        }
        
function formatNumber (n) {

            var n_array = n.toString().split('.');
            console.log(n,n_array);
            if(n_array.length == 1){
                 return n === '' ? n : Number(n).toLocaleString();
            }else{
                n = Number(n_array[0]).toLocaleString();
                if (n_array[1][0]) n +='.'+n_array[1][0];
                if (n_array[1][2]) n += n_array[1][1];
                return n;
            }
            
        }
$('button#booking_agency_details').click(function(){
      $.ajax({
        type: "POST",
        url: "/ajax/booking/getBookingAgencyDetails",
        dataType:'json',
        async: false,
        success: function(response){
          if(response.status === 'true'){

            var agencies_count = Object.keys(response.agencyBooks.data).length;
            var agencyBookHTML = '<div class="table-responsive">';
            agencyBookHTML += '<table class="table liq-agencia" border="1"><thead><tr><th>AGENCIA</th>';
            $.each(response.yearLst,function(index,year){
              if (response.agencyBooks.years[year]){
                agencyBookHTML += '<th colspan="5">TEMP '+response.agencyBooks.years[year]+'</th><th rowspan="2"></th>';
              }
            });
            agencyBookHTML += '</tr><tr><th></th>';
            for(i=0;i<3;i++){
              agencyBookHTML += '<th>Vtas</th><th>Vtas. %</th><th>Reservas</th><th>Res. %</th><th>Comisión</th>';
            }
            agencyBookHTML += '</th></thead><body>';

            x = 1;
            var agencyName = response.agencyBooks.items;
            $.each(response.agencyBooks.data,function(agency,seasons){
              agencyBookHTML += '<tr class="text-right"><td class="bold" style="font-size:16px !important;">'+agencyName[agency]+'</td>';
              var a = 2;
              $.each(seasons,function(season,data){
                  agencyBookHTML += '<td>'+formatNumber(toFixed(data.total,0))+' €</td><td>'+toFixed(data.total_rate,0)+' %</td><td>'+formatNumber(data.reservations)+'</td><td>'+toFixed(data.reservations_rate,0)+' %</td><td>'+formatNumber(toFixed(data.commissions,0))+' €</td>';
                  if (a>0){
                    agencyBookHTML += '<td style="background-color:#48b0f7;"></td>';
                  }
                  a--;
              });

              agencyBookHTML += '</tr>';

              x++;
              });

              agencyBookHTML += '<tr class="footer-table"><td>Total</td>';
              var a = 2;
              $.each(response.yearLst,function(index,year){
              if (response.agencyBooks.totals[year]){
                var aux = response.agencyBooks.totals[year];
                agencyBookHTML += '<td>'+formatNumber(aux.total,0)+'</td><td>-</td>';
                agencyBookHTML += '<td>'+aux.reservations+'</td><td>-</td>';
                agencyBookHTML += '<td>'+formatNumber(aux.commissions,0)+'</td>';
                 if (a>0){
                    agencyBookHTML += '<td style="background-color:#48b0f7;"></td>';
                  }
                  a--;
              }
              });
              agencyBookHTML += '</tr>';


              agencyBookHTML += '<tbody></table>';
              agencyBookHTML += '</div>';

            bootbox.alert({
              message: agencyBookHTML,
              size: 'large',
              backdrop: true
            });
          } else {
            bootbox.alert({
              message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. El PAN no ha sido guardado.<br/>Contacte con el administrador.</div>',
              backdrop: true
            });
          }
        },
        error: function (response) {
          bootbox.alert({
            message: '<div class="text-danger bold" style="margin-top:10px">Se ha producido un ERROR. No se ha podido obtener los detalles de la consulta.<br/>Contacte con el administrador.</div>',
            backdrop: true
          });
        }
      });
    });

  });
</script>