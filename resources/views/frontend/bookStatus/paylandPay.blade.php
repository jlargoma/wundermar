<!DOCTYPE html>
<html>
  <head>
    <title>Formulario de Pago</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
    
    <style>
     .background{height:100%;width:100%;opacity:.75;position:fixed;left:0;top:0;background-image:url("{{ assetV('img/wundermar/lockscreen.jpg')}}");background-repeat:no-repeat;background-size:cover}.contenedor{z-index:99;position:absolute;top:4em;left:0;width:100%}.title{width:100%;text-align:center;color:#fff;font-size:3em}.form{width:100%;text-align:center;color:#828282;font-size:2em;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;background:rgba(255,255,255,.6)!important;color:#000;font-size:18px;font-weight:700;margin-bottom:0;padding:7px 0 6em 0}.logo{width:320px;display:block;margin:auto}ul{width:100%;text-align:center;margin:1em auto;padding:0}li{display:inline-block}li.step{background-color:#d9e0e2;color:#496893;text-align:center;padding:13px;border-radius:48%;margin:0;width:20px}li.step.active{background-color:#496893;color:#fff}li.line{height:4px;width:4em;padding:0;border-radius:inherit;padding-bottom:4px;margin:1em 0 0 0;border-bottom:3px solid #496893}li.line span{background-color:#b3cfe2;padding:6px;color:#496893}input#dni{padding:6px;border:1px solid #3f88b8;font-size:1em;border-radius:4px}.m1{margin:1em auto}span.required{color:red}button{padding:12px;color:#eff2f3;background-color:#42648e;font-size:1em;border:none;border-radius:7px}.alert-warning{display:none;width:320px;margin:1em auto;padding:10px;background-color:#e2d9aa;color:#b56700;font-weight:400;border:1px solid #cac090;border-radius:7px}input[type=checkbox]{width:22px;height:21px;position:absolute;border:1px solid #3f88b8}span.check{position:relative;width:25px;display:inline-flex;height:1em}label.checkbox input[type=checkbox]{display:none}label.checkbox span{display:inline-block;border:2px solid #bbb;border-radius:10px;width:25px;height:25px;background:#ff5e5e;vertical-align:middle;margin:3px;position:relative;transition:width .1s,height .1s,margin .1s}label.checkbox :checked+span{background:#aceaac;width:27px;height:27px;margin:2px}label.checkbox :checked+span:after{content:'\2714';font-size:20px;position:absolute;top:2px;left:5px;color:#4087b7}.fs-2{font-size:1.3em}.loader{border:10px solid #e0e0e0;border-top:10px solid #3498db;border-radius:50%;width:30px;height:30px;margin:15px auto;animation:spin 2s linear infinite;display:none}.tpv{width:100%;text-align:center;color:#fff;font-size:2.1em;margin:0}.site-name{width:100%;text-align:center;font-size:4em;color:#fff;background-color:#6d5cae;padding:9px 0;margin-bottom:0;margin-top:5px;background-color:#3f50b5}.box{max-width:840px;margin:auto}.purchaseForms{width:100%;margin:1em auto}.purchaseForms table.table{width:100%}.purchaseForms .table-responsive{border:1px solid #3f50b5;background-color:#fff;padding:7px}.purchaseForms thead{background-color:#496893;color:#fff;text-align:center;font-size:16px;height:2em}.purchaseForms thead th{padding:8px}.purchaseForms td{padding:9px;vertical-align:top!important}i.iconminus,i.iconplus{padding:4px;cursor:pointer;font-size:20px;font-weight:600}.purchaseForms i{user-select:none;-webkit-user-select:none;-moz-user-select:none;-khtml-user-select:none;-ms-user-select:none}input.bkgSupl_check{cursor:pointer;height:20px;display:block;width:20px;float:left;margin-right:3px}.purchaseForms td.tit{text-align:left}.purchaseForms h5{font-size:17px;margin:5px 0 0 31px}.purchaseForms td.qty,.purchaseForms td.subtotal{vertical-align:top!important}.purchaseForms tr.unselect{background-color:#b5b5b5}input.bkgSupl_qty{width:25px;height:23px;text-align:center;font-weight:700;font-size:16px;margin:0;border:1px solid #c3c3c3;border-radius:5px}.purchaseForms tr.unselect td.subtotal{text-decoration:line-through;text-decoration-style:double}.purchaseForms td.qty,.purchaseForms td.subtotal{min-width:80px}.purchaseForms tfoot th{background-color:#d9e0e2;padding:8px}.tableAsigned{margin:5px auto;padding:0;font-size:15px}@media (max-width:426px){.site-name{font-size:3em}}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
     @media (max-width: 540px){
       .purchaseForms h5 {
          font-size: 15px;
       }
       .purchaseForms small {
            font-size: 12px;
        }
        .purchaseForms td.qty, .purchaseForms td.subtotal {
            min-width: 45px;
        }
        input.bkgSupl_qty {
            display: block;
            margin: 7px auto;
        }
        i.iconminus, i.iconplus {
            font-size: 40px;
        }
     }
    </style>
  </head>
  <body>
    <div class="background"></div>
    <div class="contenedor">
      <h3 class="tpv">TPV</h3>
     
        <a class="logo" href="/">
          <img src="{{ assetV('img/wundermar/logo_wundermar.png') }}" alt="wundermar">
        </a>
      
      @if($site)
      <h4 class="site-name">{{$site['name']}}</h4>
      @endif
            
      @if($request_dni)
      <div class="form black">
        <ul>
          <li id="step_1" class="active step">1</li>
          <li class="line"></li>
          <li id="step_2" class="step">2</li>
        </ul>
        <div class="">
          
          <div class=" fs-2">{{$dates}}</div>
          <h2>{{$room}}</h2>
          <div class="m1 fs-2">
            <label>Nombre:</label>
            {{$name}}
          </div>
        </div>
        <div id="form_step_1" >


          <div class="m1">
            <label><span class="required">*</span>PASSPORT / DNI:</label>
            <input type="text" id="dni" class="form-control required">
          </div>
          <div class="m1">
            <label class="checkbox">
              <input type="checkbox" id="tyc_1" >
              <span></span>
              Acepta las <a href="{{url('https://'.$site['url'].'/condiciones-de-contratacion/')}}" title="Ir a políticas de contratación" target="_black">
                políticas de contratación
              </a>
            </label>
          </div>
          @if($has_fianza)
          <div class="m1">
            <label class="checkbox">
              <input type="checkbox" id="tyc_2" >
              <span></span>
              Acepta las 
              <a href="{{route('cond.fianza')}}" title="Ir a condiciones de fianza" target="_black">
                condiciones de fianza
              </a>
            </label>
          </div>
          @endif
          <div class="text-center">
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            <button class="btn btn-primary" title="Ir al paso 2" id="siguiente">Siguente</button>
          </div>
          <p class="alert alert-warning msg-error" ></p>
          <p class="loader"></p>
        </div>
        <div id="form_step_2" style="display:none">
          <iframe src="{{ $urlPayland  }}" frameborder="0" style="width: 100%; min-height: 550px;"></iframe>
        </div>
        <div id="recaptcha" class="g-recaptcha" data-sitekey="6Ld4Jh8TAAAAAD2tURa21kTFwMkKoyJCqaXb0uoK"></div>
      </div>
      @else
      <iframe src="{{ $urlPayland  }}" frameborder="0" style="width: 100%; min-height: 550px;"></iframe>
      @endif



    </div>
    @if($request_dni)
    <script  src="https://code.jquery.com/jquery-2.1.4.min.js"
             integrity="sha256-8WqyJLuWKRBVhxXIL1jBDD7SDxU936oZkCnxQbWwJVw="
    crossorigin="anonymous"></script>
    <script>
    $(function () {
      var token = '{{csrf_token()}}';
      var url_step1 = '{{$urlSend}}';
      var url_step2 = '{{$urlSend2}}';
      
      
      $("#siguiente").on("click",function(){if($("#tyc_1").is(":checked")){var t=$("#dni").val();if(isBlank(t)||isEmpty(t))showError("Por favor, complete su CIF, NIF ó DNI para continuar");else{var e={dni:t,_token:token,accepted_hiring_policies:$("#tyc_1").is(":checked"),accepted_bail_conditions:$("#tyc_2").is(":checked")};$.ajax({url:url_step1,data:e,type:"POST",crossDomain:!0}).done(function(t){"ok"==t?($(".loader").hide(),$("#step_1").removeClass("active"),$("#step_2").addClass("active"),$("#form_step_1").hide(500,function(){$("#form_step_2").show()})):showError(t)}).fail(function(t){showError("Error de sistema")})}}else showError("Por favor, acepte las políticas de contratación para continuar")});var _formatEu=function(t){return new Intl.NumberFormat("de-DE",{style:"currency",currency:"EUR",minimumFractionDigits:0}).format(t)},_summaryRender=function(){var t=0;$("#blqSupl tr").each(function(e){var r=$(this);if(r.find(".bkgSupl_check").is(":checked")){var n=r.find(".bkgSupl_qty").val(),i=r.data("p");r.find(".subtotal").text(_formatEu(n*i)),t+=n*i}}),$("#suplTotal").text(_formatEu(t))};$("#blqSupl").on("change",".bkgSupl_qty",function(){$(this).val()<1&&$(this).val(1),_summaryRender()}),$("#blqSupl").on("click",".iconplus",function(){var t=$(this).closest("td").find(".bkgSupl_qty"),e=parseInt(t.val());t.val(e+1),_summaryRender()}),$("#blqSupl").on("click",".iconminus",function(){var t=$(this).closest("td").find(".bkgSupl_qty"),e=parseInt(t.val())-1;e<1&&(e=1),t.val(e),_summaryRender()}),$("#blqSupl").on("change",".bkgSupl_check",function(){$(this).is(":checked")?$(this).closest("tr").removeClass("unselect"):$(this).closest("tr").addClass("unselect"),_summaryRender()}),$("#siguiente_2").on("click",function(){var t=[];$("#blqSupl tr").each(function(e){var r=$(this);if(r.find(".bkgSupl_check").is(":checked"))var n={k:r.data("key"),q:r.find(".bkgSupl_qty").val()};t.push(n)});var e={_token:token,items:t};$.ajax({url:url_step2,data:e,type:"POST",crossDomain:!0}).done(function(t){"ok"==t?($(".loader").hide(),$("#step_2").removeClass("active"),$("#step_3").addClass("active"),$("#form_step_2").hide(500,function(){$("#form_step_3").show()})):showError(t)}).fail(function(t){showError("Error de sistema")})});var showError=function(t){$(".msg-error").text(t).fadeIn(),$(".loader").hide(500,function(){$("#siguiente").show()}),setTimeout(function(){$(".msg-error").text("").fadeOut()},3500)};function isBlank(t){return!t||/^\s*$/.test(t)}function isEmpty(t){return!t||0===t.length}
          });
    </script>
    @endif
  </body>
</html>