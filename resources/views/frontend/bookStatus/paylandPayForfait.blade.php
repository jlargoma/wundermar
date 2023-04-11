<!DOCTYPE html>
<html>
  <head>
    <title>Formulario de Pago</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      .background{
        height: 100%;
        width: 100%;
        opacity: 0.75;
        position: fixed;
        left: 0;
        top: 0;
        background-image: url("{{ assetV('img/miramarski/lockscreen.jpg')}}");
        background-repeat: no-repeat;
        background-size: cover;
      }
      .contenedor{
        z-index: 99;
        position: absolute;
        top: 4em;
        left: 0;
        width: 100%;
      }
      .title{
        width: 100%;
        text-align: center;
        color: #fff;
        font-size: 3em;
        font-family: sans-serif;
      }
      .form{
        width: 100%;
        text-align: center;
        color: #828282;
        font-size: 2em;
        font-family: "Helvetica Neue", "Helvetica", Arial, sans-serif;
        background: rgba(255, 255, 255, 0.6) !important;
        color: #000;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 0px;
        padding:  7px 0 6em 0;
        clear: both;
        overflow: auto;

      }

      .logo{
        width: 320px;
        display: block;
        margin: auto;
      }
      .rowns{
        clear: both;
        overflow: auto;
      }
      .col-detail{
        width: 51%;
        float: left;
        padding: 0 15px;
      }
      th {
        padding: 7px;
      }
      td{
        border-bottom: 1px solid #b3b3b3;
        padding: 15px 7px;
      }
      td.noborder,
      tr:last-child td{
            border-bottom: none;
      }
      .col-payment{
        width: 45%;
        float: left;
      }
      .cart-container h3 {
        background-color: #86a8c1;
        color: #fff;
        font-weight: 600;
        padding: 10px;
      }

      table.forfait {
        width: 99%;
        margin: 1px auto;
        background-color: rgba(255, 255, 255, 0.8);
        font-weight: 400;
      }
      tr.forfaitHeader {
        border: 1px solid #101010;
        background-color: #86a8c1;
        color: #fff;
        text-align: left;
        padding: 5em;
        height: 30px;
      }
      .forfait tr {
        border: 1px solid #101010;
        padding: 0px;
        text-align: left;
      }
      .tcenter{
        text-align: center;
      }
      .tright{
        text-align: right;
      }
      .show-mobile{
        display: none;
      }
      @media (max-width: 768px){
        .contenedor {
          top: 0em;
        }
        .col-detail,.col-payment {
          width: 96%;
          padding: 1em 2%;
          float: none;
        }
        .title{
          margin: 13px auto;
        }
        
        .show-mobile {
            display: block;
            margin-top: -146px;
            z-index: 57;
            position: absolute;
        }

        .hide-mobile{
        display: none;
        }
        .show-mobile{
        display: block;
        }
      }
    </style>
  </head>
  <body>
    <div class="background"></div>
    <div class="contenedor">
      <h1 class="title">PAGO FORFAIT</h1>
      <div class="form">
        <div class="col-detail cart-container hide-mobile">
          {!! $orderText !!}
        </div>
        <div class="col-payment">
          <iframe src="{{ $urlPayland  }}" frameborder="0" style="width: 100%; min-height: 550px;"></iframe>
        </div>
        <div class="col-detail cart-container show-mobile">
          {!! $orderText !!}
        </div>
      </div>
    </div>

  </body>
</html>