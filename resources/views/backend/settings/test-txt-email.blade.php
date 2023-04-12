<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
  <head>
    <title>Simulador de mensajes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      body {
        font-size: 15px;
      }
      .content {
        width: 99%;
        margin: 2em auto;
      }
      h2.var {
        background-color: #343975;
        color: #fff;
        padding: 8px;
        text-align: center;
      }
      .row{
        clear: both;
        overflow: auto;
      }
      .site {
        width: 28%;
        margin: 1%;
        float: left;
        border: 1px solid;
        padding: 10px;
      }
      h3.sitename {
        margin: 0 0 7px 0;
        background-color: #cacaca;
        padding: 5px;
      }


      ul.tabs-btn{
        overflow: auto;
      }
      ul.tabs-btn li {
        list-style: none;
        display: inline-block;
        margin: 7px 4px;
      }
      ul.tabs-btn li a {
        list-style: none;
        border:1px solid #004a2f;
        color: #004a2f;
        padding: 3px 7px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
      }
      ul.tabs-btn li a:hover {
        background-color: #e4def9;
      }
      ul.tabs-btn li.active a{
        background-color: #004a2f;
        color: #fff;
        font-weight: bold;
      }
      .pull-left{
        float: left;
        margin-right: 2em;
      }
      h1.pageName {
        text-align: center;
        border: 3px solid #333974;
        color: #333974;
      }
      warning{
        background-color: #fdd972;
        color: #503b00;
        border: 1px solid #f9cf59;
        padding: 7px;
        width: 96%;
        display: block;
      }
      line {
          display: block;
          height: 1px;
          clear: both;
      }
      @media only screen and (max-width: 768px){
        .site {
          width: 90%;
          float: none;
          margin: 1em auto;
        }
        ul.tabs-btn {
          padding: 0;
        }
      }
    </style>
  </head>
  <body>
    <div class="content">
      <h1 class="pageName">Simulador de Textos/Comunicados</h1>
      <div class="col-md-12">
        <ul class="tabs-btn">
          @foreach($settings as $k=>$v)
          <li  <?php if ($k == $key) echo 'class="active"'; ?>>
            <a href="/test-text/{{$lng}}/{{$k}}">{{$v}}</a>
          </li>
          @endforeach
        </ul>
      </div>

      <div class="row">
        <ul class="tabs-btn pull-left">
          <li  <?php if ($lng == 'es') echo 'class="active"'; ?>>
            <a href="/test-text/es/{{$key}}/{{$ota}}">Español</a>
          </li>
          <li <?php if ($lng == 'en') echo 'class="active"'; ?>>
            <a href="/test-text/en/{{$key}}/{{$ota}}">Ingles</a>
          </li>
        </ul>

        @if( $key == 'reservation_state_changed_reserv')
        <ul class="tabs-btn pull-left">
          <li  <?php if ($ota == 'ota') echo 'class="active"'; ?>>
            <a href="/test-text/{{$lng}}/{{$key}}/ota">OTA</a>
          </li>
          <li <?php if ($ota == '') echo 'class="active"'; ?>>
            <a href="/test-text/{{$lng}}/{{$key}}">Normal</a>
          </li>
        </ul>
        @endif
      </div>



      <?php foreach ($data as $k => $t): ?>
        <div>
          <h2 class="var">{{$settings[$k]}}</h2>
          <div class="row">
            <?php 
            $count = 0;
            foreach ($sites as $k => $sname): ?>
              <?php
              $text = '';
              $count++;
//              $varsTxt
              if (isset($t[$k])) {
                $text = $t[$k];
                if ($wsp)
                  $text = whatsappUnFormat($text);

                foreach ($varsTxt as $k => $v) {
                  $text = str_replace('{' . $k . '}', "<i>[$v]</i>", $text);
                }
              }
              if ($count == 4) echo '<line></line>';
              ?>
              <div class="site">
                <h3 class="sitename">{{$sname}}</h3>
              <?php echo $text; ?>
              </div>
  <?php endforeach; ?>
          </div>
        </div>
<?php endforeach; ?>
    </div>
  </body>
</html>