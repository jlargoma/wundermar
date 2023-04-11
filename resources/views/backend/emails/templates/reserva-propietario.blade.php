<?php use \Carbon\Carbon ;?>

<h2>Reserva de Propietario</h2>

Hola Jaime, un propietario ha bloqueado su apartamento "{room_name}".<br><br>

Nombre:<b>{customer_name}</b> <br>
Teléfono:<b><a href="tel:{customer_phone}">{customer_phone}</a></b> <br>
Apartamento: <b>{room}  {room_type}</b><br>
Email:<b>{customer_email}</b> <br>
Fecha Entrada:<b>{date_start}</b> <br>
Fecha Salida:<b>{date_end}</b> <br>
Noches:<b>{nigths}</b> <br>
Ocupantes:<b>{pax}</b> <br>
      

<b>Observaciones cliente:</b> {comment} <br/><br/>      
<b>Observaciones internas :</b> {book_comments}<br/><br/>    

                
<hr/>
<br/>Gestiona la solicitud <a href='http://www.apartamentosierranevada.net/admin'>pinchando aquí</a><br/>