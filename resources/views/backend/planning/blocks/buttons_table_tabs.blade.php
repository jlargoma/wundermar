<?php if (!$is_mobile ): ?>
  <div class="lst-tabs-btn">
    <button class="btn btn-primary  btn-blue btn-tables btn-cons" type="button" data-type="pendientes">
      <span class="bold">Pendientes</span>
      <?php if ($uRole != "agente"): ?>
        <span class="numPaymentLastBooks">
          {{ $booksCount['pending'] }}
        </span>
      <?php endif ?>
    </button>
    <button class="btn btn-primary btn-tables btn-cons" type="button" data-type="blocks" style="background-color: #448eff;">
      <span class="bold">Bloqueadas</span>
      <span class="text-black text-cont">{{ $booksCount['blocks'] }}</span>
    </button>
    <button class="btn btn-success btn-tables btn-cons" type="button" data-type="reservadas" style="background-color: #53ca57;">
      <span class="bold">Reservadas</span>
      <span class="text-black text-cont">
        {{ $booksCount['reservadas'] }}
      </span>
    </button>
    <button class="btn  btn-primary btn-green btn-tables btn-cons" type="button" data-type="confirmadas">
      <span class="bold">Confirmadas</span>
      <span class="text-black text-cont" >
        {{ $booksCount['confirmed'] }}
      </span>
    </button>
    <?php if ($uRole != "agente"): ?>
      <button class="btn btn-primary  btn-orange btn-tables btn-cons" type="button" data-type="especiales">
        <span class="bold">Especiales</span>
        <span class="text-black text-cont" >
          {{ $booksCount['special'] }}
        </span>
      </button>
    <?php endif ?>
    <?php if ($uRole != "agente"): ?>
      <button class="btn btn-success btn-tables btn-cons" type="button" data-type="checkin">
        <span class="bold">Check IN</span>
        <span class="text-black text-cont" >
          {{ $booksCount['checkin'] }}
        </span>
      </button>

      <button class="btn btn-primary btn-tables btn-cons" type="button" data-type="checkout">
        <span class="bold">Check OUT</span>
        <span class="text-black text-cont" >
          {{ $booksCount['checkout'] }}
        </span>
      </button>
      <button class="btn btn-danger btn-tables btn-cons" type="button" data-type="eliminadas">
        <span class="bold">Eliminadas</span>
        <span class="text-black text-cont" >
          {{ $booksCount['deletes'] }}
        </span>

      </button>
      <button class="btn btn-danger btn-tables btn-cons" type="button" data-type="cancel-xml">
        <span class="bold">Cancel-XML</span>
        <span class="text-black text-cont" >
          {{ $booksCount['cancel-xml'] }}
        </span>
      </button>
      <?php endif ?>
  </div>
<?php else: ?>

    <button class="btn btn-primary  btn-blue btn-tables" type="button" data-type="pendientes">
      <span class="bold">Pend</span>
      <?php if ($uRole != "agente"): ?>
        <span class="numPaymentLastBooks" style="top: 0px;right: 0;padding: 0px 7px;">
          {{ $booksCount['pending'] }}
        </span>
      <?php endif ?>
    </button>
    <button class="btn btn-success btn-tables btn-cons" type="button" data-type="blocks" style="background-color: #448eff;">
      <span class="bold">Bloq</span>
      <span class="text-black text-cont" >{{ $booksCount['blocks'] }}</span>
    </button>
    <button class="btn btn-success btn-tables btn-cons" type="button" data-type="reservadas" style="background-color: #53ca57;">
      <span class="bold">Reser</span>
      <span class="text-black text-cont" >
        {{ $booksCount['reservadas'] }}
      </span>
    </button>
    <button class="btn  btn-primary btn-green btn-tables" type="button" data-type="confirmadas">
      <span class="bold">Conf</span>
      <span class="text-black text-cont">
        {{ $booksCount['confirmed'] }}
      </span>
    </button>
    <?php if ($uRole != "agente"): ?>
      <button class="btn btn-primary  btn-orange btn-tables" type="button" data-type="especiales">
        <span class="bold">Esp</span>
        <span class="text-black text-cont">
          {{ $booksCount['special'] }}
        </span>
      </button>
    <?php endif ?>
    <?php if ($uRole != "agente"): ?>
      <button class="btn btn-success btn-tables" type="button" data-type="checkin">
        <span class="bold">IN</span>
        <span class="text-black text-cont">
          {{ $booksCount['checkin'] }}
        </span>
      </button>

      <button class="btn btn-primary btn-tables" type="button" data-type="checkout">
        <span class="bold">OUT</span>
        <span class="text-black text-cont">
          {{ $booksCount['checkout'] }}
        </span>
      </button>
      <button class="btn btn-danger btn-tables" type="button" data-type="eliminadas">
        <span class="bold">Elimin...</span>
        <span class="text-black text-cont" style="background-color: white; font-weight: 600; border-radius: 100%; padding: 5px;">
          {{ $booksCount['deletes'] }}
        </span>
      </button>
      <button class="btn btn-danger btn-tables" type="button" data-type="cancel-xml">
        <span class="bold">Cancel</span>
        <span class="text-black text-cont" style="background-color: white; font-weight: 600; border-radius: 100%; padding: 5px;">
          {{ $booksCount['cancel-xml'] }}
        </span>
      </button>
    <?php endif ?>
  </div>
<?php endif ?>