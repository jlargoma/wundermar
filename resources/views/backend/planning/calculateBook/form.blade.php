<div class="calcBkg">
  <h4>CALCULAR RESERVA</h4>
  <form id="formCalcularReserva" action="{{url('/admin/reservas/help/getTotalBook')}}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="quantity">*Personas <i class="fa fa-question-circle help" ><span> Min PAX y Max PAX del tipo de Apartamentos (Widget Habitaciones)</span></i></label>
          <input type="text" class="form-control" name="name" id="cal-nombre" placeholder="Nombre..." maxlength="40" aria-label="Escribe tu nombre" value="{{$name}}">
        </div>
      </div>
      <div class="col-md-5 col-xs-10">
        <div class="form-group">
          <label for="date" >*Entrada - Salida</label>
          <input type="text" class="form-control daterange1" id="date"   name="date" required style="cursor: pointer;text-align: center;" readonly="" value="{{$date}}">
          <input type="hidden" class="date_start" name="start" value="">
          <input type="hidden" class="date_finish" name="finish" value="">
          <p  class="help-block min-days" style="display:none;line-height:1.2;color:red;">
            <b>* ESTANCIA MÍNIMA: 2 NOCHES</b>
          </p>
        </div>
      </div>
      <div class="col-md-1 col-xs-2">
        <div class="form-group">
          <label for="date" >Noches</label>
          <input type="text" class="form-control nigths" readonly="" >
        </div>
      </div>
      <div class="col-md-3 col-xs-6">
        <div class="form-group">
          <label>Edificio</label>
          <select class="form-control minimal" name="site_id">
            <option value="0">Todos</option>
            <?php foreach (\App\Sites::all() as $item): ?>
              <option value="<?php echo $item->id ?>" <?php echo ($item->id == $site_id) ? 'selected' : '' ?>>
                <?php echo $item->name ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
      <div class="col-md-3 col-xs-6">
        <div class="form-group">
          <label for="quantity">*Personas</label>
          <div class="quantity">
            <select name="quantity" class="form-control minimal" name="quantity">
              <?php for ($i = 1; $i <= 14; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $pax) ? 'selected' : '' ?>><?php echo $i ?></option>  
              <?php endfor ?>
            </select>
          </div>
          <p class="help-block hidden-sm hidden-xs" style="line-height:1.2">Máx 12</p>
        </div>
      </div>
      <div class="col-md-3 col-xs-6">
        <div class="form-group">
          <label>Tipo Apto</label>
          <select class="form-control minimal" name="size_apto_id">
            <option value="0">Todos</option>
            <?php foreach (\App\SizeRooms::allSizeApto() as $k => $v): ?>
              <option value="{{$k}}">{{$v}}</option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
      <div class="col-md-3 col-xs-6">
        <div class="form-group  text-center ">
          <br/>
          <button type="submit" class="btn btn-success btn-cons btn-lg" id="confirm-reserva">Calcular reserva</button>
        </div>
      </div>
    </div>
  </form>
  <div class="row" id="calcReserv_result" style="display: none;">

  </div>
</div>
<script type="text/javascript" src="{{ assetV('/js/datePicker01.js')}}"></script>
<script type="text/javascript" src="{{ assetV('/js/backend/calculateBook.js')}}"></script>

<style>
  .calcBkg {
    padding: 7px;
    overflow: hidden;
    border: 2px solid #295d9b;
    margin: 3px 0px 30px;
  }
  .calcBkg h4{
    background-color: #295d9b;
    color: white;
    text-align: center;
    padding: 4px;
    text-align: center;
  }
  i.fa.fa-question-circle {
    color: #d0893e;
    margin-left: 3px;
    font-size: 15px;
  }
  .help span{
    visibility: hidden;
    background-color: #424242;
    display: block;
    padding: 6px;
    font-size: 12px;
    position: absolute;
    color: #FFF;
    text-transform: initial;
    line-height: 1.2;
  }
  .help:hover span{
    visibility: visible;
  }
  @media (max-width: 425px){
    #confirm-reserva.btn-cons {
      min-width: 3em!important;
      width: 100%;
      max-width: 253px;
      height: 35px;
      margin-top: 5px;
      padding: 0px;
    }
  }
</style>