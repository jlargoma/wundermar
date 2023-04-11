<div class="row">
  <?php $dateAux = $startYear->copy(); ?>
  <?php $diffInMonths = $startYear->diffInMonths($endYear) + 1; ?>
  <div class="col-12" style="overflow-x: auto;">
    <?php for ($i = 1; $i <= $diffInMonths; $i++) : ?>
      <?php $monthAux = $dateAux->copy()->format('n'); ?>
      <button <?php if ($monthAux == $currentM): ?>id="btn-active"<?php endif ?> class='btn btn-rounded btn-sm btn-default btn-fechas-calendar reloadCalend' data-month="<?php echo $monthAux; ?>" data-time="<?php echo $dateAux->timestamp; ?>">
        <?php echo getMonthsSpanish($monthAux) . ' ' . ucfirst($dateAux->copy()->formatLocalized('%y')) ?>
      </button>
      <?php $dateAux->addMonth(); ?>
    <?php endfor; ?>
  </div>
</div>