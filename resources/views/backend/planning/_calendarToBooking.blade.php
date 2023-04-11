<?php   use \Carbon\Carbon;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
use Illuminate\Support\Facades\Cache;

?>
<style type="text/css">
    .calendar-day {
        width: 20px;
        height: 15px;
        float: left;
        text-align: center;
    }
    .minimal span{
        color: red;
    }
    .critical {
        background: red;
    }
    .critical span{
        color: white;
    }
</style>
<div class="row">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
            style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="pg-close fs-20" style="color: #000!important;"></i>
    </button>
</div>
<div class="row">
    <h2 class="text-center font-w300">
        Calendario <span class="font-w800">BOOKING</span>
    </h2>
</div>
<div class="row push-40">
    <div class="col-xs-12" style="padding: 0px 20px;">
        <ul class="nav nav-tabs nav-tabs-simple bg-info-light fechas" role="tablist"
            data-init-reponsive-tabs="collapse">
			<?php $dateAux = $dateX->copy()->firstOfMonth(); ?>
			<?php for ($i = 1; $i <= 9 ; $i++) :?>
			<?php if (!$mobile) {
				$hidden = "";
			} else {
				$hidden = "hidden";
			} ?>
            <li class='<?php if ($i == 4) {
				echo "active";
			} ?> <?php if ($i < 4 && $i > 8) {
				echo $hidden;
			} ?>'>
                <a href="#booking<?php echo $i?>" data-toggle="tab" role="tab" style="padding:10px"
                   data-month="<?php echo $i?>">
                  
                  <?php $monthAux = $dateAux->copy()->format('n');?>
                <?php echo getMonthsSpanish($monthAux).' '.ucfirst($dateAux->copy()->formatLocalized('%y'))?>
		<?php //echo ucfirst($dateAux->copy()->formatLocalized('%b %y'))?>
                </a>
            </li>
			<?php $dateAux->addMonth(); ?>
            
			<?php endfor; ?>
        </ul>
        <div class="tab-content">
	        <?php $dateAux = $dateX->copy(); ?>
			<?php for ($z = 1; $z <= 9; $z++):?>
            <div class="tab-pane <?php if ($z == 4) {
				echo 'active';
			} ?>" id="booking<?php echo $z ?>" style="padding: 0 5px;">
                <div class="row">
                    <div class="table-responsive">
                        <table class="fc-border-separate calendar-table" style="width: 100%">
                            <thead>
                            <tr>
                                <td rowspan="2" style="width: 1%!important"></td>
								<?php for ($i = 1; $i <= $arrayMonths[$dateAux->copy()->format('n')] ; $i++): ?>
                                <td style='border:1px solid black;width: 3%;font-size: 10px' class="text-center">
									<?php echo $i?>
                                </td>
								<?php endfor; ?>
                            </tr>
                            <tr>

								<?php for ($i = 1; $i <= $arrayMonths[$dateAux->copy()->format('n')] ; $i++): ?>
                                <td style='border:1px solid black;width: 3%;font-size: 10px'
                                    class="text-center <?php echo $days[$dateAux->copy()->format('n')][$i]?>">
									<?php echo $days[$dateAux->copy()->format('n')][$i]?>
                                </td>
								<?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody>
							<?php $inx = 0; ?>
							<?php foreach ($typesRoom as $key => $room): ?>
                            <tr>
                                <td class="text-center " style='width: 3%;text-align: center'>
                                    <b>
										<?php echo substr($room['name'], 0, 5)?>
                                        <span class="text-danger">(<?php echo $room['total'] ?>)</span>
                                    </b>
                                </td>
								<?php for ($i = 1; $i <= $arrayMonths[$dateAux->copy()->format('n')] ; $i++): ?>
								    <?php $minimal = ($room['months'][$dateAux->copy()->format('n')][$i] == 1) ?
								    "minimal": ""?>
	                                <?php $critical = ($room['months'][$dateAux->copy()->format('n')][$i] == 0) ?
		                            "critical": ""?>
                                    <td class="<?php echo  $minimal." ".$critical ?>" style='border:1px solid grey;
                                    width:
                                    3%;text-align:
                                    center'>
                                        <!--<b><?php echo $dateAux->copy()->format('n')?></b>-->
                                        <span><?php echo $room['months'][$dateAux->copy()->format('n')][$i]?></span>
                                    </td>
								<?php endfor; ?>

                            </tr>
							<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php $dateAux->addMonth() ?>
			<?php endfor; ?>
        </div>
    </div>
</div>