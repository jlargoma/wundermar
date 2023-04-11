<div class="modal fade slide-up in" id="season" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
              <i class="pg-close fs-14"></i>
            </button>
            <h2 class="text-center">
              Temporada
            </h2>
          </div>
          <div class="block block-content" id="contentSeason" style="padding:20px">
            @include('backend.seasons.create')
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="modal fade slide-up in" id="typeSeason" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
              <i class="pg-close fs-14"></i>
            </button>
            <h2 class="text-center">
              Tipos de Temporada
            </h2>
          </div>
          <div class="block block-content" style="padding:20px">
            @include('backend.seasons.typesSeason.create')
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade slide-up in" id="temporadas" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="pg-close fs-14"></i>
          </button>
          <h2 class="text-center">
            Definir Temporadas
          </h2>
        </div>
        <div class="block block-content" style="padding:20px">
          <div class="row temporadas" >
            <form action="{{ route('years.change.month') }}" method="POST" id="defineSeason">
              <div class="col-md-3">
                <label>Temporada</label>
                @include('backend.years._select')
              </div>
              <div class="col-md-3">
                <label>Desde</label>
                <input type="text" name="start" id="year_start" class="datepicker2" value="<?php echo date('d/m/Y', strtotime($year->start_date)); ?>">
              </div>
              <div class="col-md-3">
                <label>Hasta</label>
                <input type="text" name="end" id="year_end" class="datepicker2" value="<?php echo date('d/m/Y', strtotime($year->end_date)); ?>">
              </div>
              <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-success">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade slide-up in" id="segment" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                class="pg-close fs-14"
                style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center">
              Rangos
            </h2>
          </div>
          <div class="block block-content" id="contentSegments" style="padding:20px">
            @include('backend.segments.create')
          </div>
        </div>
      </div>
    </div>
  </div>
</div>