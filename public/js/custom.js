$(document).on('mouseover','[data-toggle="tooltip"]',function(){
    $(this).tooltip();
});

$(document).ready(function() { 
  window["show_notif"] = function(title,status,message = ''){
    $(document).find('.notificate').remove();
    var icon = 'fa';
    switch(status){
      case "success":
        icon += ' fa-check';
        break;
      case "error":
        icon += ' fa-exclamation';
        break;
      case "success":
        icon += ' fa-exclamation';
        break;
    }
    var titleVar = '';
    if (title !== '') titleVar = '<strong>'+title+'</strong>';
    $.notify({
          title: titleVar,
          icon: icon,
          message: message
      },{
          type: status,
          animate: {
              enter: 'animated fadeInUp notificate',
              exit: 'animated fadeOutRight'
          },
          placement: {
              from: "top",
              align: "left"
          },
          offset: 20,
          spacing: 10,
          z_index: 1031,
          allow_dismiss: true,
          delay: 1000,
          timer: 3000,
      }); 
    }
  
   
  window["formatDate"] = function (date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
  }
  
    window["formatterEuro"] =  new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0
     })
    
  $('.only-numbers').keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32, 107, 17, 67, 86, 88,188]) !== -1 ||
    // Allow: Ctrl/cmd+A
    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: Ctrl/cmd+C
    (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: Ctrl/cmd+X
    (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: home, end, left, right
     (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
      
  });
  
/*******   msgEmergente   ***********/
  var timeoutMsgEmergente = null;
  var msgEmergente = function(id,offset,content){
    
    if(timeoutMsgEmergente) clearTimeout(timeoutMsgEmergente);
    
    if ($('#msgEmergente_'+id).length>0){
      $('#msgEmergente_'+id).remove();
    } else {
      $('.msgEmergente').remove();
      var pop = $('<div class="msgEmergente"></div>');
      pop.attr('id','msgEmergente_'+id);
      
      var wBody = $('body').width();
      var hBody = $('body').height();
      
      if (wBody>740){
        var left = (offset.left+5);
        var top = (offset.top+10);
        var max_w = wBody-left;
        var max_h = hBody-top;
        pop.css({
          'left' : left+'px',
          'top' : top+'px',
          'max-width':max_w+'px',
          'max-height':max_h+'px',
        });
      } else {
        var top = (offset.top+10);
        var max_h = hBody-top;
        pop.css({
          'top' : top+'px',
          'max-height':max_h+'px',
        });
      }
      
      pop.html(content);
      $('body').append(pop);
      
      timeoutMsgEmergente = setTimeout(function(){$('#msgEmergente_'+id).remove();},5000);
    }
  }
  $('body').on('mouseover','.seeContentPop',function(){
    msgEmergente(
            $(this).data('id'),
            $(this).offset(),
            $(this).data('content')
    );
  });
 $('body').on('click','.seeContentPop',function(){
    msgEmergente(
            $(this).data('id'),
            $(this).offset(),
            $(this).data('content')
    );
  });
/*******   msgEmergente   ***********/

/******* BEGIN: copy CKEditor   ***********/
function convertToPlain(html){

    // Create a new div element
    var tempDivElement = document.createElement("div");

    // Set the HTML content with the given value
    tempDivElement.innerHTML = html;
console.log('tempDivElement',tempDivElement);
    // Retrieve the text property of the element 
    return tempDivElement.textContent || tempDivElement.innerText || "";
}

$('body').on('click','.btnCopyCKEDITOR', function(){
  var str = CKEDITOR.instances[$(this).data('instance')].getData();
 
  str = (convertToPlain(str));
  var testingCodeToCopy = document.querySelector('#copyCKEditorCode')
  testingCodeToCopy.value = str;
  testingCodeToCopy.select()
  var successful = document.execCommand('copy');
  if (successful) alert("Mensaje Copiado");
  else alert("texto no copiado");
          
});
/******* END: copy CKEditor   ***********/
});

function nl2br (str, is_xhtml) {
  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}


