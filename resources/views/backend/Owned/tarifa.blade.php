<div class="container-fluid padding-10 sm-padding-10">
    <div class="row">
        <div class="row">
            <div  class="col-xs-12 text-center push-20"><h2 class="font-w800">Tarifas</h2></div>
            <div class="col-md-6">
                <?php use \Carbon\Carbon; ?>
                <?php
                    $dateD          = new Carbon('first day of September 2018');
                    $seasonTemp    = \App\Seasons::where('start_date', '>=', $dateD->copy())
                                                 ->where('finish_date', '<=', $dateD->copy()->addYear())
                                                 ->orderBy('start_date', 'ASC')
                                                 ->get();
                    $auxSeasonType = \App\TypeSeasons::orderBy('order', 'ASC')->get();
                ?>
                  
                @include('backend.seasons.calendar', [
                                                        	'seasons'    => $auxSeasonType,
                                                            'newseasons' => $auxSeasonType,
                                                            'extras'     => \App\Extras::all(),

                                                            'seasonsTemp'        => $seasonTemp,
                                                            'newtypeSeasonsTemp' => $auxSeasonType,
                                                            'typeSeasonsTemp'    => $auxSeasonType,
                                                            'date'               => $dateD,
                                                        ])

            </div>
            <div class="col-md-6 table-responsive">
                <style>
                    .Alta{
                        background: #f0513c;
                    }
                    .Media{
                        background-color: #127bbd;
                    }
                    .Baja{
                        background-color: #91b85d;

                    }
                    .Premium{
                        background-color: #ff00b1;
                        color: white;
                    }
                    .extras{
                        background-color: rgb(150,150,150);
                    }
                </style>
                <?php $seasons = \App\TypeSeasons::all() ?>
                <table class="table" >
                    <thead>
                        <tr>
                            <th class ="text-center bg-white text-complete" style="width: 3%" rowspan="2"> &nbsp;&nbsp; &nbsp;   </th>
                            <?php foreach ($seasons as $key => $season): ?>
                                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" style="width: 20%" colspan="3"> <?php echo $season->name ?> </th>
                            <?php endforeach ?>
                        </tr>
                        <tr>                          
                            <?php foreach ($seasons as $key => $season): ?>
                                <th class ="text-center bg-complete text-white <?php echo $season->name ?>" colspan="3" style="width: 10%">Precio</th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=4; $i <= 14 ; $i++): ?>
                            <?php if ($i >= $room->minOcu && $i <= $room->maxOcu):?>
                            <tr>
                                <td class ="text-center font-s16 font-w600" style="padding: 10px 5px"> 
                                  <b><?php echo $i ?> Pers.</b>
                                </td>
                                <?php foreach ($seasons as $key => $season): ?>
                                    <?php $price =  \App\Prices::where('occupation', $i)->where('season', $season->id )->first(); ?>
                                    <?php 
                                    if ($price): ?>
                                        <td  class ="text-center font-s16 font-w600" style="padding: 10px 5px" colspan="3"> 
                                            <?php echo round( $price->cost ); ?>€
                                        </td>
                                       
                                    <?php else: ?>
                                        <td  class ="text-center font-s16 font-w600" style="padding: 10px 5px" colspan="3"></td>
                                    <?php endif ?>
                                    
                                <?php endforeach ?>
                            </tr>
                            <?php endif?>
                        <?php endfor?>
                    </tbody>
                </table>

            </div>
        </div>
        
        <div class="container ">
            <div class="m-t-20">
                <p class="text-justify" style="font-size: 18px">
                    Con la finalidad de aumentar la ocupación en los días valle vamos a sacar una oferta de 3 x 2 días en noches de entre semana (de domingo a jueves) y siempre que no coincida con ningún puente o festivo de alta disponibilidad. <br><br>

                    Esta promoción no se realizará por defecto ni para todos los apartamentos, si no en función de cómo vaya la ocupación y del consentimiento de cada propietario:<br><br>

                    <input type="checkbox"><b>Autorizo a que se realice la oferta 3x2 en mi apartamento, siempre y cuando me informen previamente de las fechas en las que se realizará la promoción.</b>
                </p>
            </div>
        </div>
    </div>
</div>
