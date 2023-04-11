<html>
  <head>
    <style>
      body{
        font-family: sans-serif;
        font-size: 11px;
      }
      @page {
        margin: 160px 50px;
      }
      header { 
        position: fixed;
        left: 0px;
        top: -160px;
        right: 0px;
        height: 100px;
        background-color: #295d9b;
        text-align: center;
        color: #fff;
      }
      header h1{
        margin: 10px 0;
      }
      header h2{
        margin: 0 0 10px 0;
      }
      footer {
        position: fixed;
        left: 0px;
        bottom: -50px;
        right: 0px;
        height: 40px;
        border-bottom: 2px solid #ddd;
      }
      footer .page:after {
        content: counter(page);
      }
      footer p {
        text-align: right;
      }

      table {
        border-collapse: collapse;
      }

      .content{
        margin: 0;
        padding: 0;
      }
      table, th, td {
        border-bottom:  1px solid #cecece;
      }
      th, td {
        padding: 8px;
      }

      table.table {
        width: 100%;
        margin: 15px auto;
      }

      table.table .header{
        background-color: #48b0f7;

      }
      .text-center{
        text-align: center;
      }
      .text-rigth{
        text-align: right;
      }
      th.header.text-left {
          text-align: left;
      }
    </style>
  <body>
    <header>
      <h1>Costos de limpieza</h1>
      <h2>{{$tit}}</h2>
    </header>
    <footer>
      <p class="page">
        Página
      </p>
    </footer>
    <div id="content">
        <table class="table">
          <thead >
            <tr>
              <th class ="header text-left">Nombre</th>
              <th class ="header text-center">T</th>
              <th class ="header text-center">Pax</th>
              <th class ="header text-center">apto</th>
              <th class ="header text-center">checkIn - checkOut</th>
              <th class ="header text-center">N</th>
              <th class ="header text-rigth">Limpieza<br><b>&#8364;&nbsp;{{$total_limp}}</b></th>
              <th class ="header text-rigth">Extras<br><b>&#8364;&nbsp;{{$total_extr}}</b></th>
            </tr>
          </thead>
          <tbody >
            @foreach($month_cost as $item)
            <tr>
              <td colspan="6">{{$item['concept']}} ({{$item['date_text']}})</td>
              <td class="text-rigth">{{moneda($item['import'])}}</td>
              <td></td>
            </tr>
            @endforeach
           
            @foreach($respo_list as $item)
            @if ($item['limp']>0 || $item['extra']>0)
            <tr>
              <td>{{$item['name']}}</td>
              <td class="text-center">{{$item['type']}}</td>
              <td class="text-center">{{$item['pax']}}</td>
              <td class="text-center">{{$item['apto']}}</td>
              <td class="text-center">{{$item['check_in']}} - {{$item['check_out']}}</td>
              <td class="text-center">{{$item['nigths']}}</td>
              <td class="text-rigth">{{moneda($item['limp'])}}</td>
              <td class="text-rigth">{{moneda($item['extra'])}}</td>
            </tr>
            @endif
            @endforeach
            <tr>
              <td colspan="8"></td>
            </tr>
            <tr>
              <td colspan="6"><strong>Totales</strong></td>
              <td class="text-rigth">
                {{$total_limp}}
              </td>
              <td class="text-rigth">{{$total_extr}}</td>
            </tr>
          </tbody>
        </table>
    </div>
  </body>
</html>