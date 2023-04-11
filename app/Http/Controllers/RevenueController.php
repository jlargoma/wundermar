<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Carbon\Carbon;
use App\Http\Controllers\INEController;
use App\Revenue;
use App\RevenuePickUp;
use App\Rooms;
use App\Book;
use App\BookDay;
use App\Services\Wubook\RateChecker;
use Excel;

class RevenueController extends AppController
{


  public function index(Request $req){
    
    $oYear  = $this->getActiveYear();
//    \App\BookDay::createSeasson($oYear->start_date,$oYear->end_date);
    
    $year   = $oYear->year;
    $month  = $req->input('month',date('n'));
    $oServ = new \App\Services\RevenueService();
    $oServ->setDates($month,$year);
    $monthStart = $oServ->start;
    $monthFinish = $oServ->finish;
    $oServ->setBook();
    $oServ->setRooms();
    $oServ->createDaysOfMonths($year);
    $ADR_finde = $oServ->getADR_finde();
    $datosMes = view('backend.revenue.dashboard.mes',[
        'books' => $oServ->books,
        'roomCh' => $oServ->rChannel,
        'roomSite' => $oServ->rSite,
        'aSites' => $oServ->aSites,
        'days' => $oServ->days,
        'months' => $oServ->months,
        'month' =>$month,
        'nights'=>$oServ->countNightsSite(),
        'rvas'=>$oServ->countBookingsSite(),
        'ADR_finde'=>$ADR_finde,
    ]);

    /*************************************************************/
    $oLiquidacion = new \App\Liquidacion();
    $dataSeason = $oLiquidacion->getBookingAgencyDetailsBy_date($oYear->start_date,$oYear->end_date);
    $agencias = view('backend.revenue.dashboard.agencias',[
        'data' => $dataSeason,
        'agencyBooks' => $oLiquidacion->getArrayAgency()
    ]);
    /*************************************************************/
    $disponiblidad = $this->data_disponibilidad($oYear,null,$month,$oServ->start,$oServ->finish,true);
    /*************************************************************/
    $oServ->start  = $oYear->start_date;
    $oServ->finish = $oYear->end_date;
    /*************************************************************/
    $oServ->setBook(); 
    $bookingCount = $oServ->countBookingsSiteMonths();
    $ADR_finde = $oServ->getADR_finde();
    $auxADR = $ADR_finde[0];
    $aRatios = $oServ->getRatios($oYear->year);
    $auxADR = $aRatios[0][0];
    $ADR_semana = $auxADR['c_s'] > 0 ? $auxADR['t_s'] / $auxADR['c_s'] : $auxADR['t_s'];
    $ADR_finde  = $auxADR['c_f'] > 0 ? $auxADR['t_f'] / $auxADR['c_f'] : $auxADR['t_f'];
    
    $viewRatios = [
        'books' => $oServ->books,
        'aRatios' => $aRatios,
        'roomCh' => $oServ->rChannel,
        'roomSite' => $oServ->rSite,
        'aSites' => $oServ->aSites,
        'days' => $oServ->days,
        'months' => $oServ->months,
        'year' =>$oYear->year,
        'mDays' =>$oServ->mDays,
        'yDays' =>$oServ->mDays[0],
        'time_start' => strtotime($oYear->start_date),
        'time_end' =>strtotime($oYear->end_date),
        'rvas'=>$oServ->countBookingsSite(),
        'summary' => $oLiquidacion->summaryTemp($oYear),
        'ADR_semana'=>moneda($ADR_semana),
        'ADR_finde'=>moneda($ADR_finde),
    ];
    $ratios = view('backend.revenue.dashboard.ratios',$viewRatios);
    /*************************************************************/
    // COMPARATIVA INGRS ANUALES
    $viewRatios['comparativaAnual'] = $oServ->comparativaAnual(date('Y'));
    $comp_ingresos_anuales = view('backend.revenue.dashboard.comp_ingresos_anuales',$viewRatios);
    /*************************************************************/
    $oFixCosts = \App\FixCosts::where('year',$oYear->year)->get();
    $oFCItems = \App\FixCosts::getLst();
    $fixCosts  = [];
    $fixCostsMonths  = [0=>0];
    
//    foreach ($oServ->months as $k=>$v) $fixCostsMonths[$k] = 0;
    foreach ($oServ->aSites as $k=>$v) $fixCosts[$k] = [];
    foreach ($oFixCosts as $fc){
      if (!isset($fixCosts[$fc->site_id][$fc->concept])){
        $fixCosts[$fc->site_id][$fc->concept] = $fixCostsMonths;
      }
      
      $fixCosts[$fc->site_id][$fc->concept][$fc->month] = intval($fc->content);
    }          
    $presupuesto = view('backend.revenue.dashboard.presupuesto',[
        'aRatios' => $aRatios,
        'aSites' => $oServ->aSites,
        'roomSite' => $oServ->rSite,
        'months' => $oServ->months,
        'year' =>$oYear->year,
        'days' => $oServ->days,
        'mDays' =>$oServ->mDays,
        'fixCosts' => $fixCosts,
        'FCItems' => $oFCItems,
        'time_start' => strtotime($oYear->start_date),
        'time_end' =>strtotime($oYear->end_date),
        'month' =>$month,
        'bookingCount'=>$bookingCount,
        'monthlyLimp'=>$oServ->getMonthSum('cost_limp', 'finish', $oYear->start_date, $oYear->end_date),
        'monthlyOta'=>$oServ->getMonthSum('PVPAgencia', 'finish', $oYear->start_date, $oYear->end_date),
        'comisionesTPV' => $oServ->commisionTPVBookingsSiteMonths()
    ]);

    
    $presupuesto_head = view('backend.revenue.dashboard.presupuesto_head',[
        'aSites' => $oServ->aSites,
        'roomSite' => $oServ->rSite,
        'month' =>$month,
        'months' => $oServ->months,
        'presupuesto' => $presupuesto,
        ]);
    /*************************************************************/
//    $oServ->setBook(); 
    $oYear = $this->getActiveYear();
    $liq = LiquidacionController::static_prepareTables($oYear);
    $aux = $oServ->getExtras();
    $liq['extrasList']    = $aux[0];
    $liq['months_extras'] = $aux[1];
    $liq['extraTit']      = $aux[2];
    $liq['months']        = $oServ->months;
    $liq['ingrSite']      = $oServ->getIngrSite($liq['siteRooms']);
    $liq['ingrMonths']    = $oServ->getIngrMonths($liq['siteRooms']);
    $ingrMes = view('backend.revenue.dashboard.ingresos',$liq);
    /*************************************************************/
    $balance = view('backend.revenue.dashboard.balance',[
          'lstMonths'=>$oServ->months,
          'ingr'=>$liq['ingrMonths'],
          'gastos'=>$oServ->getExpenses($year),
          'year'=>$oYear,
          'extrasList'=>$liq['extrasList'],
          'extraTit'=>$liq['extraTit'],
          'siteRooms'=>$liq['siteRooms'],
          'ingrExt'=>$oServ->getIncomesYear($oYear->year)
          ]);
    /*************************************************************/    
    
    return view('backend.revenue.dashboard',[
        'datosMes' => $datosMes,
        'year' => $year,
        'month' =>$month,
        'disponiblidad' => $disponiblidad,
        'ingrMes' => $ingrMes,
        'balance' => $balance,
        'ratios' => $ratios,
        'presupuesto_head' => $presupuesto_head,
        'agencias' => $agencias,
        'comp_ingresos_anuales' => $comp_ingresos_anuales,
      ]);
    
  }
  
  function getMonthKPI($month){
    $oYear   = $this->getActiveYear();
    $oServ = new \App\Services\RevenueService();
    $oServ->setDates($month,$oYear->year,$oYear);
    $oServ->setBook();
    $oServ->setRooms();
    $ADR_finde = $oServ->getADR_finde();
    
    return view('backend.revenue.dashboard.mes',[
        'books' => $oServ->books,
        'roomCh' => $oServ->rChannel,
        'roomSite' => $oServ->rSite,
        'aSites' => $oServ->aSites,
        'days' => $oServ->days,
        'months' => $oServ->months,
        'month' =>$month,
        'nights'=>$oServ->countNightsSite(),
        'rvas'=>$oServ->countBookingsSite(),
        'ADR_finde'=>$ADR_finde,
    ]);
  }
  
  function updOverview(Request $req){
    $year = $req->input('y');
    $month = $req->input('m');
    $key = $req->input('k');
    $value = $req->input('v');
    $site = $req->input('s');
    $monthSelect = $req->input('ms');
    $d = [];
    
    if ($month>0){
      $sSummary = \App\Settings::findOrCreate('revenue_disponibilidad_'.$year.'_'.$month, $site);
    } else {
      $sSummary = \App\Settings::findOrCreate('revenue_disponibilidad_'.$year, $site);
    }
    $d = json_decode($sSummary->content, true);
    if (!is_array($d)) $d = [];
    $d[$key] = $value;
    
    $sSummary->content = json_encode($d);
    $sSummary->save();
    
    return $this->getOverview($monthSelect);
  }
  function getOverview($month){
    $oYear   = $this->getActiveYear();
    $oServ = new \App\Services\RevenueService();
    $oServ->setDates($month,$oYear->year,$oYear);
    $oServ->start  = $oYear->start_date;
    $oServ->finish = $oYear->end_date;
    $oServ->setRooms();
    $oServ->createDaysOfMonths($oYear->year);
    $oServ->setBook();
   
    
    $oFixCosts = \App\FixCosts::where('year',$oYear->year)->get();
    $oFCItems = \App\FixCosts::getLst();
    $fixCosts  = [];
    $fixCostsMonths  = [0=>0];
    foreach ($oServ->aSites as $k=>$v) $fixCosts[$k] = [];
    foreach ($oFixCosts as $fc){
      if (!isset($fixCosts[$fc->site_id][$fc->concept])){
        $fixCosts[$fc->site_id][$fc->concept] = [0=>0];
      }
      $fixCosts[$fc->site_id][$fc->concept][$fc->month] = intval($fc->content);
      $fixCosts[$fc->site_id][$fc->concept][0] += intval($fc->content);
    }    
    
    return view('backend.revenue.dashboard.presupuesto',[
        'aRatios' => $oServ->getRatios($oYear->year),
        'aSites' => $oServ->aSites,
        'roomSite' => $oServ->rSite,
        'months' => $oServ->months,
        'year' =>$oYear->year,
        'days' => $oServ->days,
        'mDays' =>$oServ->mDays,
        'fixCosts' => $fixCosts,
        'FCItems' => $oFCItems,
        'time_start' => strtotime($oYear->start_date),
        'time_end' =>strtotime($oYear->end_date),
        'month' =>$month,
        'ratios' => $oServ->getRatios(null),
        'bookingCount'=>$oServ->countBookingsSiteMonths(),
        'monthlyLimp'=>$oServ->getMonthSum('cost_limp', 'finish', $oYear->start_date, $oYear->end_date),
        'monthlyOta'=>$oServ->getMonthSum('PVPAgencia', 'finish', $oYear->start_date, $oYear->end_date),
        'comisionesTPV' => $oServ->commisionTPVBookingsSiteMonths()
    ]);
  }
  
  function getMonthDisp($month){
    $oYear   = $this->getActiveYear();
    $oServ = new \App\Services\RevenueService();
    $oServ->setDates($month,$oYear->year,$oYear);
    return $this->data_disponibilidad($oYear,null,$month,$oServ->start,$oServ->finish,true);
  }

  /*******************************************************************************/
  /*******************************************************************************/
  /*******************************************************************************/
  
  public function disponibilidad(Request $req,$return=false){
    $oYear   = $this->getActiveYear();
    $site_id  = $req->input('site',2);
    $month = $req->input('month',date('m'));
    $start  = firstDayMonth($oYear->year, $month);
    $finish = lastDayMonth($oYear->year, $month);
    return $this->data_disponibilidad($oYear,$site_id,$month,$start,$finish,false);
  }
  
  function data_disponibilidad($oYear,$site_id,$month,$start,$finish,$return=false){
    $chNames = configOtasAptosName(null);
    $lstMonths = getMonthsSpanish(null,FALSE, TRUE);
    /************************************************************/
    /********   Prepare days array               ****************/
    $startTime = strtotime($start);
    $endTime = strtotime($finish);
    $aLstDays = [];
    
    $dw = listDaysSpanish(true);
    $dwMin = ['D','L','M','M','J','V','S'];
    $startAux = $startTime;
    while ($startAux<=$endTime){
      $aLstDays[date('j_m_y',$startAux)] = $dw[date('w',$startAux)];
      $aLstDaysMin[date('j_m_y',$startAux)] = $dwMin[date('w',$startAux)];
      $startAux = strtotime("+1 day", $startAux);
    }
    /************************************************************/
    /********   Get Roooms                      ****************/
    $allRooms = \App\Rooms::where('state',1)
            ->where('channel_group','!=','')->get();
    $otas = [];
    $otaSite = [];
    $roomsID = [];
    $totalOtas = 0;
    foreach ($allRooms as $room){
      if (isset($otas[$room->channel_group])){
        $otas[$room->channel_group]++;
      } else {
        $otaSite[$room->channel_group] = $room->site_id;
        $otas[$room->channel_group] = 1;
      }
      $totalOtas++;
      $roomsID[] = $room->id;
    }
    
    
    /************************************************************/
    $allSites = \App\Sites::allSitesKey();
    $lstBySite = [];
    $startAux = $startTime;
    $aLstNightEmpty = [];
    while ($startAux <= $endTime) {
      $aLstNightEmpty[date('j_m_y', $startAux)] = 0;
      $startAux = strtotime("+1 day", $startAux);
    }
    foreach ($allSites as $sID=>$n){
        $oRooms = Rooms::where('site_id',$sID)->get();
        $rIDs = [];
        $avail = 0;
        foreach ($oRooms as $r){
          $rIDs[] = $r->id;
          if ($r->channel_group) $avail++;  // OVERBOOKING
        }
        $books  = BookDay::where_book_times($start,$finish)
                ->whereIn('type', Book::get_type_book_sales(true,true))
                ->whereIn('room_id', $rIDs)
                ->get();
        $aLstNight = $aLstNightEmpty;

        $tNigh = 0;
        $tPvp  = 0;

        $control = [];
        if ($books) {
          foreach ($books as $b) {
            $auxTime = date('j_m_y', strtotime($b->date));
            if (isset($aLstNight[$auxTime]))  $aLstNight[$auxTime]++;
            $tPvp  += $b->pvp;
          }
        }
        
        $lstBySite[$sID] = [
            'days'=>$aLstNight,
            'avail'=>$avail,
            'tNigh'=>array_sum($aLstNight),
            'tPvp'=>$tPvp,
        ];
    }
    /*****************************************************************/
    /********   Get Bookings                     ****************/  
    $listDaysOtas = [];
    $oBook = new \App\Book();
    foreach ($otas as $apto=>$v){
      $listDaysOtas[$apto] = $oBook->getAvailibilityBy_channel($apto, $start, $finish,false,false,true);
    }
    $listDaysOtasTotal = null;
    foreach ($listDaysOtas as $k=>$v){
      if (!$listDaysOtasTotal) $listDaysOtasTotal = $v;
      else {
        foreach ($v as $d=>$v2){
          $listDaysOtasTotal[$d]+= $v2;
        }
      }
    }
    /*********************************************************************/
    /**********   SUMMARY                                 ****************/
    $totalMonth = $totalMonthOcc = $totalOtas*count($aLstDays);
    $nightsTemp = calcNights($oYear->start_date,$oYear->end_date);
    $totalSummary = $totalOtas*$nightsTemp;
    $totalSummaryOcc = 0;
    $monthPVP = $summaryPVP = 0; 

    $sqlBooks = \App\Book::where_type_book_sales(true,true)->whereIn('room_id',$roomsID);
    $books = \App\Book::w_book_times($sqlBooks, $start, $finish)->get();
                    
    if ($books){
      $startMonth = strtotime($start);
      $endMonth = strtotime($finish);
      $totalMonthOcc = 0;
      foreach ($books as $book){
        $summaryPVP += $book->total_price;
        if (date('m',strtotime($book->start)) == $month || date('m',strtotime($book->finish)) == $month){
          $pvpPerNigh = ($book->nigths>0) ? $book->total_price/$book->nigths : 0;
          $startAux = strtotime($book->start);
          $finishAux = strtotime($book->finish);
          while ($startAux<$finishAux){
            if (date('m',$startAux) == $month){
              $monthPVP += $pvpPerNigh;
              $totalMonthOcc++;
            }
            $startAux = strtotime("+1 day", $startAux);
          }
        }
      }
    }
    //Total by seasson
    $totalSummaryOcc = \App\Book::whereIn('type_book', [1,2,11])
          ->where([['start','>=', $oYear->start_date ],['finish','<=', $oYear->end_date ]])
          ->whereIn('room_id',$roomsID)->sum('nigths');

    $occupPerc = ($totalMonth>0) ? (round($totalMonthOcc/$totalMonth*100)) : 0;
    $medPVP = ($totalMonthOcc>0) ? ($monthPVP/$totalMonthOcc) : 0;
    $occupPercSession = ($totalSummary>0) ? (round($totalSummaryOcc/$totalSummary*100)) : 0;
    $medPVPSession = ($totalSummaryOcc>0) ? ($summaryPVP/$totalSummaryOcc) : 0;
    
    /************************************************************/
    /*******  BY SETTING SUMMARY    ****************************/
    $sKeys = [
        'pres_n_hab'=>0,'pres_n_hab_perc'=>0,'pres_med_pvp'=>0,'pres_pvp'=>0,
        'foresc_n_hab'=>0,'foresc_n_hab_perc'=>0,'foresc_med_pvp'=>0,'foresc_pvp'=>0
        ];
    
    $sSummarySeasson = \App\Settings::findOrCreate('revenue_disponibilidad_'.$oYear->year, $site_id);
    $sSummaryMonth = \App\Settings::findOrCreate('revenue_disponibilidad_'.$oYear->year.'_'.$month, $site_id);
    $sSummarySeasson = json_decode($sSummarySeasson->content,true);
    $sSummaryMonth = json_decode($sSummaryMonth->content,true);
    $sSummarySeasson = $sSummarySeasson ? array_merge($sKeys,$sSummarySeasson) : $sKeys;
    $sSummaryMonth = $sSummaryMonth ? array_merge($sKeys,$sSummaryMonth) : $sKeys;
    
    $sSummarySeasson['pres_n_hab_perc'] = ($totalSummary>0) ? (round($sSummarySeasson['pres_n_hab']/$totalSummary*100)) : 0;
    $sSummarySeasson['foresc_n_hab_perc'] = ($totalSummary>0) ? (round($sSummarySeasson['foresc_n_hab']/$totalSummary*100)) : 0;
    $sSummaryMonth['pres_n_hab_perc'] = ($totalMonth>0) ? (round($sSummaryMonth['pres_n_hab']/$totalMonth*100)) : 0;
    $sSummaryMonth['foresc_n_hab_perc'] = ($totalMonth>0) ? (round($sSummaryMonth['foresc_n_hab']/$totalMonth*100)) : 0;
        
    /************************************************************/
    $mmonths = getMonthsSpanish(null,true,true);
    unset($mmonths[0]);
    /************************************************************/
    
    $data = [
      'year' => $oYear,
      'otas' => $otas,
      'chNames' => $chNames,
      'aLstDays' => $aLstDays,
      'listDaysOtas' => $listDaysOtas,
      'totalOtas'=>$totalOtas,
      'listDaysOtasTotal'=>$listDaysOtasTotal,
      'lstMonths' => $lstMonths,
      'mmonths' => $mmonths,
      'otaSite' => $otaSite,
      'month_key' => $oYear->year.'_'.$month,
      'month' => intVal($month),
      'totalMonthOcc' => $totalMonthOcc,
      'totalSummaryOcc' => $totalSummaryOcc,
      'totalMonth' => $totalMonth,
      'totalSummary' => $totalSummary,
        'summaryPVP' => $summaryPVP,
        'monthPVP' => $monthPVP,
        'occupPerc' => $occupPerc,
        'occupPercSession' => $occupPercSession,
        'medPVP' => $medPVP,
        'medPVPSession' => $medPVPSession,
        'sSummarySeasson' => $sSummarySeasson,
        'sSummaryMonth' => $sSummaryMonth,
        'lstBySite'=>$lstBySite,
        'allSites'=>$allSites,
        'aLstDaysMin'=>$aLstDaysMin,
    ];
    if ($return)  return view('backend.revenue.dashboard.disponibilidad',$data );
    return view('backend.revenue.disponibilidad',$data );
  }
 
  /**
   * UPD disponibilidad resumen
   * @param Request $req
   */
  public function updDisponib(Request $req) {
    $key = $req->input('key',null);
    $id  = $req->input('id',null);
    $val = $req->input('input',0);
    $site = $req->input('site',1);
    
    if (!$key || !$id ){
      return response()->json(['status'=>'error','msg'=>'las claves no existen']);
    }
    
    $sSummary = \App\Settings::findOrCreate('revenue_disponibilidad_'.$key, $site);
    if (!$sSummary){
      return response()->json(['status'=>'error','msg'=>'Registro no encontrado']);
    }
    $aSummary = json_decode($sSummary->content,true);
    if (!$aSummary){
      $sKeys = [
        'pres_n_hab'=>0,'pres_n_hab_perc'=>0,'pres_med_pvp'=>0,'pres_pvp'=>0,
        'foresc_n_hab'=>0,'foresc_n_hab_perc'=>0,'foresc_med_pvp'=>0,'foresc_pvp'=>0
        ];
      if (isset($sKeys[$id])){
        $sKeys[$id] = $val;
        $sSummary->content = json_encode($sKeys);
        $sSummary->save();
        return response()->json(['status'=>'OK']);
      }
    }
    if (isset($aSummary[$id])){
      $aSummary[$id] = $val;
      $sSummary->content = json_encode($aSummary);
      $sSummary->save();
      return response()->json(['status'=>'OK']);
    }
    return response()->json(['status'=>'error','msg'=>'Registro no actualizado']);
    
  }
  /**
   * Generate data from INE analytic
   * @param Request $req
   * @return type
   */
  public function donwlDisponib(Request $req) {
    
    $oYear   = $this->getActiveYear();
    $lstMonths = getMonthsSpanish(null,FALSE, TRUE);
    $site_id  = $req->input('site',2);
    $month = $req->input('month',date('m'));
    $start  = firstDayMonth($oYear->year, $month);
    $finish = lastDayMonth($oYear->year, $month);
    
    $chNames = configOtasAptosName($site_id);
    /************************************************************/
    /********   Prepare days array               ****************/
    $startAux = strtotime($start);
    $endAux = strtotime($finish);
    $aLstDays = [];
    
    $dw = listDaysSpanish(true);
    while ($startAux<=$endAux){
      $aLstDays[date('d',$startAux)] = $dw[date('w',$startAux)];
      $startAux = strtotime("+1 day", $startAux);
    }
      
    /************************************************************/
    /********   Get Roooms                      ****************/
    $qry_ch = \App\Rooms::where('state',1);
    if ($site_id>0){
      $qry_ch->where('site_id',$site_id);
    }
    $allRooms = $qry_ch->where('channel_group','!=','')->get();
    

    $otas = [];
    $roomsID = [];
    $totalOtas = 0;
    foreach ($allRooms as $room){
      if (isset($otas[$room->channel_group])){
        $otas[$room->channel_group]++;
      } else {
        $otas[$room->channel_group] = 1;
      }
      $totalOtas++;
    }
    
    
    /************************************************************/
    /********   Get Bookings                     ****************/  
    $listDaysOtas = [];
    $oBook = new \App\Book();
    foreach ($otas as $apto=>$v){
      $listDaysOtas[$apto] = $oBook->getAvailibilityBy_channel($apto, $start, $finish,false,false,true);
    }
    $listDaysOtasTotal = null;
    foreach ($listDaysOtas as $k=>$v){
      if (!$listDaysOtasTotal) $listDaysOtasTotal = $v;
      else {
        foreach ($v as $d=>$v2){
          $listDaysOtasTotal[$d]+= $v2;
        }
      }
    }
    /************************************************************/
    
   
    $rowTit = ['',''];
    $listMonth = [];
    foreach($aLstDays as $d=>$w) $rowTit[] = $d;
    
    
    foreach($otas as $ch=>$nro){
      $chName = isset($chNames[$ch]) ? $chNames[$ch] : '-';
      $aux = [$chName,'Total'];
      foreach($aLstDays as $d=>$w){
        $aux[] = $nro;
      }
      $listMonth[] = $aux;
      ////////////////////////////////////////////////
      $aux = ['','Libres'];
      foreach($listDaysOtas[$ch] as $avail){
        $aux[] = ($avail>0) ? $avail : '-';
      }
      $listMonth[] = $aux;
      ////////////////////////////////////////////////
      $aux = ['','Ocupadas'];
      foreach($listDaysOtas[$ch] as $avail){
        $aux[] = $nro-$avail;
      }
      $listMonth[] = $aux;
    }
    ////////////////////////////////////////////////
    $aux = ['TOTAL','Total'];
    foreach($aLstDays as $d=>$w){
      $aux[] = $totalOtas;
    }
    $listMonth[] = $aux;
    ////////////////////////////////////////////////
    $aux = ['','Libres'];
    foreach($listDaysOtasTotal as $v){
      $aux[] = $v;
    }
    $listMonth[] = $aux;
    ////////////////////////////////////////////////
    $aux = ['','Ocupadas'];
    foreach($listDaysOtasTotal as $v){
      $aux[] = $totalOtas-$v;
    }
    $listMonth[] = $aux;
    ////////////////////////////////////////////////  
    
    $name = 'PickUp_'. $start.'_al_'.$finish;
    \Excel::create($name, function($excel)  use($rowTit,$listMonth)  {
       $excel->sheet("Mensual", function($sheet) use($rowTit,$listMonth) {
            $sheet->freezeFirstColumn();
            $sheet->row(1, $rowTit);
            $index = 2;
            foreach($listMonth as $r) {
                $sheet->row($index, $r); 
                $index++;
            }
            
        });
//       $excel->sheet($site_2, function($sheet) use($oRevenue_2) {
//            $sheet->freezeFirstColumn();
//            $sheet->row(1, [
//            ]);
//
//            $index = 2;
//            foreach($oRevenue_2 as $r) {
//              $day = date('m/d/Y', strtotime($r->day));
//                $sheet->row($index, [
//                    $day,$r->llegada,$r->ocupacion,$r->salida,$r->llegada+$r->ocupacion,
//                    $r->disponibilidad,$r->get_ocup_percent(),$r->cancelaciones,
//                    ($r->ingresos),'-',($r->extras),($r->ingresos+$r->extras),
//                    $r->disponibilidad,$r->get_ADR(),($r->ingresos),$r->get_ADR(),''
//                ]); 
//                $index++;
//            }
//            
//        });
         
        })->export('xlsx');
  }
  /***************************************************************************/
  /***************************************************************************/
  /***************************************************************************/
  public function rate_shopper() {
    
    $oRateChecker = new RateChecker();
    
    $Competitors = $oRateChecker->getCompetitorsData();
    $Competitors = $oRateChecker->getRateData($Competitors);
    
    /********************/
    $rooms = ['Double or Twin Room','Superior Double or Twin Room'];
    $byRoom = [];
    $competitorsID = [];
    foreach ($Competitors as $k=>$v){
      $competitorsID[$k] = $v['name'];
      if (is_array($v['snaphot']['lstRooms']))
        foreach ($v['snaphot']['lstRooms'] as $lstRooms){
          $rName = $lstRooms['name'];
          //filtra habitaciones
          if (!in_array($rName, $rooms))  continue;
          
          
          if (!isset($byRoom[$rName])) $byRoom[$rName] = [];
          if (!isset($byRoom[$rName][$k])) $byRoom[$rName][$k] = [];
          foreach ($lstRooms['prices'] as $kPrice=>$price){
            if (isset($byRoom[$rName][$k][$kPrice])){
              if (is_numeric($price))
                $byRoom[$rName][$k][$kPrice] = ($byRoom[$rName][$k][$kPrice]+$price)/2;
            } else {
              $byRoom[$rName][$k][$kPrice] = intval($price);
            }
          }
        }
    }
    /********************/
    $range = [];
    $oneDay = 24 * 60 * 60;
    $wDay = listDaysSpanish(true);
    for($i=0;$i<$oRateChecker->maxRange;$i++){
      $time = $oRateChecker->startRange+($oneDay*$i)+1;
      $range[date('Ymd',$time)] = $wDay[date('w',$time)].' '.date('d/m',$time);
    }
    $oYear = $this->getActiveYear();
    return view('backend.revenue.rate-shopper',[
      'year' => $oYear,
      'competitors' => $Competitors,
      'byRoom' => $byRoom,
      'competitorsID' => $competitorsID,
      'range' => $range,
    ]);
    
  }
  
  
  public function setRateCheckWubook() {
    
    $oRateChecker = new RateChecker();
//    $oRateChecker->setCompetitorsData();
    $oRateChecker->setRateData();
  }
  
  
  
  
  
  /****************************************************************************/
  
  
  public function pickUp(Request $req){
    
    $oYear   = $this->getActiveYear();
    $months = getMonthsSpanish(null,false,true);

    
    $ch  = $req->input('ch_sel',null);
    $site_id  = $req->input('site',null);
    $start  = $req->input('start',null);
    $finish = $req->input('finish',null);
    $sel_mes = $req->input('sel_mes',date('m'));
    if ($sel_mes) {
      $mesAux = $oYear->year.'-'.$sel_mes.'-01';
      $start = $mesAux;
      $d = \DateTime::createFromFormat('Y-m-d',$mesAux);
      $finish = $d->modify('last day of this month')->format('Y-m-d');
    }
    $qry_ch = \App\Rooms::where('state',1);
    if ($site_id>0){
      $qry_ch->where('site_id',$site_id);
    }
    $allChannels = $qry_ch->where('channel_group','!=','')
            ->groupBy('channel_group')->pluck('channel_group')->all();
    
    
    $qryRevenue = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]]);
    
    if ($site_id>0){
      $qryRevenue->where('site_id',$site_id);
    }
    if ($ch){
      $qryRevenue->where('channel',$ch);
    }
    
    $allRevenue = $qryRevenue->get();
    
    if (!$ch){
      if (count($allRevenue)){
        $ProcessPickUp = new \App\Models\ProcessPickUp();
        $allRevenue = $ProcessPickUp->compactRevenue($allRevenue);
      }
    }
       
    $summary = [];
    $tOcup = 0;
    $tDisp = 0;
    $tIng = 0;
        
    $qrySumm = RevenuePickUp::whereYear('day','=',$oYear->year);
    if ($site_id>0) $qrySumm->where('site_id',$site_id);
    if ($ch) $qrySumm->where('channel',$ch);
    $allSummay = $qrySumm->get();
    $sM = [];
    if (count($allSummay))
      foreach ($allSummay as $r){
        $m = date('n', strtotime($r->day));
        if (!isset($sM[$m])) $sM[$m] = ['tOcup'=>0,'tDisp'=>0,'tIng'=>0];
        
        $sM[$m]['tOcup'] += $r->ocupacion+$r->llegada;
        $sM[$m]['tDisp'] += $r->disponibilidad;
        $sM[$m]['tIng']  += $r->ingresos;
        
        $tOcup += $r->ocupacion+$r->llegada;
        $tDisp += $r->disponibilidad;
        $tIng  += $r->ingresos;
      }
      
    /************************************************************************/  
     if (count($sM))
      foreach ($sM as $m=>$v){
        $sM[$m]['perc'] = round( $v['tOcup']*100/ $v['tDisp']);
        $sM[$m]['pm']   = ($v['tOcup']>0) ? moneda($v['tIng']/$v['tOcup']) : '-';
        $sM[$m]['tIng'] = moneda($v['tIng']);
        
      }  
    /************************************************************************/
      
      
      
    
    $PickUpEvents = \App\RevenuePickUpEvents::where([['date','>=', $start ],['date','<=', $finish ]])->get();
    $lstPickUpEvents = [];
    if ($PickUpEvents){
      foreach ($PickUpEvents as $item){
        $lstPickUpEvents[$item->date] = $item->event;
      }
    }
    
    
    $oLiquidacion = new \App\Liquidacion();
    if ($ch) $roomsID = \App\Rooms::where('channel_group',$ch)->pluck('id');
    else  $roomsID = \App\Rooms::where('site_id',$site_id)->pluck('id');
    
    $dataSeason = $oLiquidacion->getBookingAgencyDetailsBy_date($start,$finish,$roomsID);
    
    $agencyBooks = $oLiquidacion->getArrayAgency();
    
    return view('backend.revenue.pickUp',[
      'year' => $oYear,
      'sel_mes' => $sel_mes,
      'months' => $months,
      'allRevenue' => $allRevenue,
      'lstPickUpEvents' => $lstPickUpEvents,
      'tOcup' => $tOcup,
      'tDisp' => ($tDisp>0) ? $tDisp : 1,
      'tIng' => $tIng,
      'summMonth' => $sM,
      'site' => $site_id,
      'start'=>$start,
      'finish'=>$finish,
      'channels' => $allChannels,
      'ch_sel' => $ch,
      'range'=>date('d M, y', strtotime($start)).' - '.date('d M, y', strtotime($finish)),
      'totalSeason' => $dataSeason['totals'],
      'dataSeason' => $dataSeason['data'],
      'agencyBooks' => $agencyBooks,
    ]);
  }
  
   /**
   * Generate data from INE analytic
   * @param Request $req
   * @return type
   */
  public function donwlPickUp(Request $req) {
    
    $oYear   = $this->getActiveYear();
    $start  = $oYear->start_date;
    $finish = $oYear->end_date;
   
    
//    $ch  = $req->input('ch_sel',null);
    $ProcessPickUp = new \App\Models\ProcessPickUp();
    $site_id  = $req->input('site',2);
    $start  = $req->input('start',null);
    $finish = $req->input('finish',null);
    
    if (!$start) $start  = $oYear->start_date;
    if (!$finish) $finish = $oYear->end_date;

    
    $qryRevenue = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]]);
    
    /******************************************************/
    /******   RIAD                      *******************/
    $site_id = 1;
    $site_1='RIAD';
    $oRevenue_1 = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]])
            ->where('site_id',$site_id)
            ->get();
    $oRevenue_1 = $ProcessPickUp->compactRevenue($oRevenue_1);
        
    /******************************************************/
    /******   HOTEL ROSA DE ORO         *******************/
    $site_id = 2;
    $site_2='Rosa D\'Oro';
    $oRevenue_2 = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]])
            ->where('site_id',$site_id)
            ->get();
    $oRevenue_2 = $ProcessPickUp->compactRevenue($oRevenue_2);
    /******************************************************/
    /******   HOTEL GLORIA         *******************/
    $site_id = 3;
    $site_3='Hotel Gloria';
    $oRevenue_3 = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]])
            ->where('site_id',$site_id)
            ->get();
    $oRevenue_3 = $ProcessPickUp->compactRevenue($oRevenue_3);
    /******************************************************/
    /******   Siloe Plaza         *******************/
    $site_id = 5;
    $site_5='Siloe Plaza';
    $oRevenue_5 = RevenuePickUp::where([['day','>=', $start ],['day','<=', $finish ]])
            ->where('site_id',$site_id)
            ->get();
    $oRevenue_5 = $ProcessPickUp->compactRevenue($oRevenue_5);
    /******************************************************/
    
    
    
    $name = 'PickUp_'. $start.'_al_'.$finish;
    $exelData = [
        [$site_1,$oRevenue_1],
        [$site_2,$oRevenue_2],
        [$site_3,$oRevenue_3],
        [$site_5,$oRevenue_5],
            ];
    
    \Excel::create($name, function($excel)  use($exelData)  {
        foreach ($exelData as $item){
            
            $site = $item[0];
            $oRevenue = $item[1];
            $excel->sheet($site, function($sheet) use($oRevenue) {
                $sheet->freezeFirstColumn();
                $sheet->row(1, [
                    'Fecha','Llegadas','Ocupadas','Salidas','Hab. Vend.','Hab. Disp.','Ocup.','Hab. Canceladas.','Prod. Hab.','Prod. Pen.','Prod. Extra','Total',
                    'PUP','ADR','Revenue','ADR ACTUAL','ADR PREVIO' 

                ]);

                $index = 2;
                foreach($oRevenue as $r) {
                    $day = date('d/m/Y', strtotime($r->day));
                    $sheet->row($index, [
                        $day,$r->llegada,$r->ocupacion,$r->salida,$r->llegada+$r->ocupacion,
                        $r->disponibilidad,$r->get_ocup_percent(),$r->cancelaciones,
                        ($r->ingresos),'-',($r->extras),($r->ingresos+$r->extras),
                        $r->disponibilidad,$r->get_ADR(),($r->ingresos),$r->get_ADR(),''
                    ]); 
                    $index++;
                }

            });
            
        }
      
         
        })->export('xlsx');
  }
   /**
   * Generate data from INE analytic
   * @param Request $req
   * @return type
   */
  public function generatePickUp(Request $req) {
    
    $oYear   = $this->getActiveYear();
    $start  = $oYear->start_date;
    $finish = $oYear->end_date;
    $ProcessPickUp = new \App\Models\ProcessPickUp();
    $site_id  = 1;
    $ProcessPickUp->bySiteChannel($site_id,$start,$finish);
    $site_id  = 2;
    $ProcessPickUp->bySiteChannel($site_id,$start,$finish);
    $site_id  = 3;
    $ProcessPickUp->bySiteChannel($site_id,$start,$finish);
    $site_id  = 4;
    $ProcessPickUp->bySiteChannel($site_id,$start,$finish);
    $site_id  = 5;
    $ProcessPickUp->bySiteChannel($site_id,$start,$finish);
    return back()->with('success','Registros generados');
  }
   
  
  function updPickUp(Request $req){
    
    $date = $req->input('date');
    $val = $req->input('val');

    $oObj = \App\RevenuePickUpEvents::where('date',$date)->first();
    if (!$oObj){
      $oObj = new \App\RevenuePickUpEvents();
      $oObj->date = $date;
    }
    
    $oObj->event = $val;
    $oObj->save();
    
    return 'OK';
  }
  /****************************************************************************/
  function daily(Request $req){
    
    $oYear   = $this->getActiveYear();
    $ch  = $req->input('ch_sel',null);
    $site_id  = $req->input('site',0);
    $start  = $req->input('start',null);
    $finish = $req->input('finish',null);
    $sel_mes = $req->input('sel_mes',date('m'));
    
    if (!$start) $start  = $oYear->start_date;
    if (!$finish) $finish = $oYear->end_date;
    return view('backend.revenue.vtas-dia',
            $this->get_dailyData($oYear,$sel_mes,$ch,$site_id));
  }
  
  function get_dailyData($oYear,$sel_mes,$ch,$site_id){
    
    $months = getMonthsSpanish(null,false,true);
    $qry_rooms = \App\Rooms::where('state',1);
    if ($site_id>0){
      $qry_rooms->where('site_id',$site_id);
    }
    $query2 = clone $qry_rooms;
    
    $allChannels = $query2->where('channel_group','!=','')
            ->groupBy('channel_group')->pluck('channel_group')->all();
    
    /***********************************************************/
    $roomsID = null;
    if ($ch){
        $roomsID = \App\Rooms::where('channel_group',$ch)->pluck('id');
        $lstRooms = \App\Rooms::where('channel_group',$ch)->pluck('name','id');
    }
    if ($site_id>0){
        $roomsID = \App\Rooms::where('site_id',$site_id)->pluck('id');
        $lstRooms = \App\Rooms::where('site_id',$site_id)->pluck('name','id');
    }
    if (!$roomsID) $lstRooms = \App\Rooms::pluck('name','id');
    /***********************************************************/
    $allSites = \App\Sites::allSites();
    /***********************************************************/
    $agency = \App\Book::listAgency();
    $oCountry = new \App\Countries();
    /***********************************************************/
    $oBooks = new \App\Book();
    $qBooks = \App\Book::where('type_book','!=',0)->where('type_book','!=',4)
            ->whereYear('created_at', '=', $oYear->year)
            ->whereMonth('created_at', '=', $sel_mes);
 
    if ($roomsID && count($roomsID)>0) $qBooks->whereIn('room_id',$roomsID);
    $oBoks = $qBooks->orderBy('created_at')->get();
     
    $lstResul = [];
    $lstResulID = [];
    if ($oBoks){
        foreach ($oBoks as $b){
            
            $time = strtotime($b->created_at);
            $day = date('Ymd',$time);
            if (isset($lstResulID[$day])) $lstResulID[$day]= [];
            $lstResulID[$day][] = $b->id;
            
            $n  = $b->nigths;
            $tp = $b->total_price;
            $adr = ($n>0) ? $b->total_price/$n : $b->total_price;
            switch ($b->type_book){
                case 1:
                case 2:
                case 7:
                case 8:
                    $status = 'Aceptada';
                    break;
                case 3:
                case 5:
                case 9:
                case 11:
                case 99:
                    $status = 'En espera';
                    break;
                case 4: $status = 'Bloqueado'; break;
                case 6: $status = 'Denegada'; break;
                case 10: $status = 'Overbooking'; break;
                case 98: $status = 'cancel-XML'; break;
                case 0: $status = 'ELIMINADA'; break;
                default:$status = ' - '; break;
            }
            
            if ($b->user_id == 98) $ch = 'WEBDIRECT';
            else $ch = isset($agency[$b->agency]) ? $agency[$b->agency] : 'Directa';
                
            $lstResul[$b->id] = [
                'create' => convertDateToShow_text(date('Y-m-d',$time),true),
                'name' => $b->customer->name,
                'in'=>convertDateToShow_text($b->start,true),
                'end'=>convertDateToShow_text($b->finish,true),
                'site_id'=> isset($allSites[$b->room->site_id])?$allSites[$b->room->site_id]:'',
                'nigth'=> $n,
                'adr'=> round($adr),
                'price'=> round($tp),
                'status'=> $status,
                'ch'=> $ch,
                'country'=> $oCountry->getCountry($b->customer->country),
                
            ];
            
        }
    }
    
    
    
    /*************************************************************************/
    /****   SUMMARY YEAR                             *************************/
    $oYear   = $this->getActiveYear();
    $start_year  = $oYear->start_date;
    $finish_year = $oYear->end_date;
        
    
    $qBooks = \App\Book::where('type_book','!=',0)->where('type_book','!=',4)
            ->where([['created_at', '>=', $start_year], ['created_at', '<=', $finish_year]]);
    if ($roomsID && count($roomsID)>0) $qBooks->whereIn('room_id',$roomsID);
    $oBoks = $qBooks->orderBy('created_at')->get();
    
    $t_n = 0;
    $t_tp = 0;
    $aTotal = [];
    for($i=1;$i<13;$i++){
      $aTotal[$i] = ['n'=>0,'tp'=>0,'adr'=>0,];
    }
    if ($oBoks){
      foreach ($oBoks as $b){
        $month = date('n', strtotime($b->created_at));
        $n = $b->nigths;
        $tp = $b->total_price;
        $t_n += $n;
        $t_tp += $tp;
        $aTotal[$month]['n'] += $n;
        $aTotal[$month]['tp'] += $tp;
      }
    }
    
    foreach ($aTotal as $k=>$v){
      $aTotal[$k]['adr'] = ($v['n']>0) ? moneda($v['tp']/$v['n']) : '--';
      $aTotal[$k]['tp'] = moneda($v['tp']);
    }
    
    $t_adr = ($t_n>0) ? ($t_tp/$t_n) : 0;
    
    /****   SUMMARY YEAR                             *************************/
    /*************************************************************************/
        return [
            'year' => $oYear,
            'lstResul' => $lstResul,
            'site' => $site_id,
            'sel_mes' => $sel_mes,
            'months' => $months,
            'start'=>null,
            'finish'=>null,
            'channels' => $allChannels,
            'ch_sel' => $ch,
            'allSites'=> $allSites,
            'range'=>null,
            't_n'=>$t_n,
            't_tp'=>$t_tp,
            't_adr'=>$t_adr,
            'aTotal'=>$aTotal,
        ];
    }
    
    function donwlDaily(Request $req){
         
        $oYear   = $this->getActiveYear();
        $ch  = $req->input('ch_sel',null);
        $site_id  = $req->input('site',0);
        $start  = $req->input('start',null);
        $finish = $req->input('finish',null);
        if (!$start) $start  = $oYear->start_date;
        if (!$finish) $finish = $oYear->end_date;
        
        
        
    $data = $this->get_dailyData($oYear,$start,$finish,$ch,$site_id);   
    $name = 'Ventas-dia-'. $start.'-al-'.$finish;
    if (is_numeric($data['site']) && isset($data['allSites'][$data['site']])){
        $name.= '-'. str_replace(' ','-', $data['allSites'][$data['site']]);
    }
    if ($data['ch_sel']) $name.= '-'.$data['ch_sel'];
    
    $lstResul = $data['lstResul'];
    $rowTit = [
        'Creada','CLIENTE','Check In',
        'Check Out','Edificio','Nº NOCHES',
        'ADR','PVP RVA','ESTADO DE RESERVA',
        'CANAL','ORIGEN: Cliente'
    ];
    \Excel::create($name, function($excel)  use($rowTit,$lstResul)  {
       $excel->sheet("Mensual", function($sheet) use($rowTit,$lstResul) {
            $sheet->freezeFirstColumn();
            $sheet->row(1, $rowTit);
            $index = 2;
            foreach($lstResul as $r) {
                $sheet->row($index, $r); 
                $index++;
            }
            
        });
        })->export('xlsx');
    }

  
    function updFixedcosts(Request $req){
      $y = $req->input('y');
      $m = $req->input('m');
      $key = $req->input('key');
      $site = $req->input('site');
      $val = $req->input('val');
      if (!is_numeric($val)){
        return 'Valor no válido';
      }
      $oObject = \App\FixCosts::where('year',$y)
              ->where('month',$m)
              ->where('site_id',$site)
              ->where('concept',$key)
              ->first();
      if (!$oObject){
        $oObject = new \App\FixCosts();
        $oObject->month   = $m;
        $oObject->year    = $y;
        $oObject->concept = $key;
        $oObject->site_id = $site;
      }
      $oObject->content = $val;
      $oObject->save();
      
      $totalSite =  \App\FixCosts::where('year',$y)
              ->where('month',$m)
              ->where('site_id',$site)
              ->sum('content');
//      $totalConcept =  \App\FixCosts::where('year',$y)
//              ->where('concept',$key)
//              ->where('site_id',$site)
//              ->sum('content');
      $totalYear =  \App\FixCosts::where('year',$y)
              ->where('month',0)
              ->where('site_id',$site)
              ->sum('content');
      return response()->json([
          'status'=>'OK',
          'totam_mensual'=> intVal($totalSite),
//          'concept_year'=> intVal($totalConcept),
          'total_year'=> intVal($totalYear),
          ]);
    }
    
    function getComparativaAnual($year){
      $oYear = \App\Years::where('year', $year)->first();
      if (!$oYear) die('Temporada no existente');
      $oServ = new \App\Services\RevenueService();
      $oServ->setDates(null,$oYear->year,$oYear);
      $oServ->setRooms();
      $oServ->setBook();
      $oServ->createDaysOfMonths($year);
      $aRatios = $oServ->getRatios($year);
      $auxADR = $aRatios[0][0];
      $ADR_semana = $auxADR['c_s'] > 0 ? $auxADR['t_s'] / $auxADR['c_s'] : $auxADR['t_s'];
      $ADR_finde  = $auxADR['c_f'] > 0 ? $auxADR['t_f'] / $auxADR['c_f'] : $auxADR['t_f'];
      $oLiquidacion = new \App\Liquidacion();
          
      $viewRatios = [
          'books' => $oServ->books,
          'aRatios' => $aRatios,
          'roomCh' => $oServ->rChannel,
          'roomSite' => $oServ->rSite,
          'aSites' => $oServ->aSites,
          'days' => $oServ->days,
          'months' => $oServ->months,
          'year' =>$year,
          'mDays' =>$oServ->mDays,
          'yDays' =>$oServ->mDays[0],
          'time_start' => strtotime($oYear->start_date),
          'time_end' =>strtotime($oYear->end_date),
          'rvas'=>$oServ->countBookingsSite(),
          'summary' => $oLiquidacion->summaryTemp($oYear),
          'ADR_semana'=>moneda($ADR_semana),
          'ADR_finde'=>moneda($ADR_finde),
      ];
      /*************************************************************/
      // COMPARATIVA INGRS ANUALES
      $viewRatios['comparativaAnual'] = $oServ->comparativaAnual($year);
      return view('backend.revenue.dashboard.comp_ingresos_anuales',$viewRatios);
    }
    
    
    function getFixedcostsAnual($year){
      $oYear = \App\Years::where('year', $year)->first();
      if (!$oYear) die('Temporada no existente');
      
      $oServ = new \App\Services\RevenueService();
      $oServ->setDates(0,$oYear->year,$oYear);
      $oServ->setRooms();

      $oFixCosts = \App\FixCosts::where('year',$oYear->year)->get();
      $oFCItems = \App\FixCosts::getLst();
      $fixCosts  = [];
      $fixCostsMonths  = [0=>0];
      foreach ($oServ->months as $k=>$v) $fixCostsMonths[$k] = 0;
      foreach ($oServ->aSites as $k=>$v) $fixCosts[$k] = [];
      foreach ($oFixCosts as $fc){
        if (!isset($fixCosts[$fc->site_id][$fc->concept])){
          $fixCosts[$fc->site_id][$fc->concept] = $fixCostsMonths;
        }

        $fixCosts[$fc->site_id][$fc->concept][$fc->month] += intval($fc->content);
      }       
    
      return view('backend.revenue.dashboard.presupuesto-modal',[
          'aSites' => $oServ->aSites,
          'months' => $oServ->months,
          'year' =>$oYear->year,
          'days' => $oServ->days,
          'fixCosts' => $fixCosts,
          'FCItems' => $oFCItems
      ]);
    }
    function copyFixedcostsAnualTo($year,$siteID){
      
      $oFixCosts = \App\FixCosts::getByYear($year-1,$siteID);
      if (count($oFixCosts)==0) return 'No hay datos cargados';
        
      \App\FixCosts::deleteByYear($year,$siteID);
      
      foreach ($oFixCosts as $item){
        $oObject = new \App\FixCosts();
        $oObject->year    = $year;
        $oObject->month   = $item->month;
        $oObject->concept = $item->concept;
        $oObject->site_id = $siteID;
        $oObject->content = $item->content;
        $oObject->save();
      }
      
     
      return 'OK';
    }
    
    function balanceAnioNatural($site='all',$year=null,$trim=null){
    if (!$year ) $year = date('Y');
    
    
    $monthEmpty = [
        '01'=>0,
        '02'=>0,
        '03'=>0,
        '04'=>0,
        '05'=>0,
        '06'=>0,
        '07'=>0,
        '08'=>0,
        '09'=>0,
        '10'=>0,
        '11'=>0,
        '12'=>0
    ];
   
    
    /************************************************/
    $sqlBooks = BookDay::where_type_book_sales();
    if ($trim){
      $trim = intval($trim);
      switch ($trim){
        case 1:
          $startTrim = $year.'-01-01';
          $endTrim = $year.'-04-01';
          $monthEmpty = ['01'=>0,'02'=>0,'03'=>0];
          break;
        case 2:
          $startTrim = $year.'-04-01';
          $endTrim = $year.'-07-01';
          $monthEmpty = ['04'=>0,'05'=>0,'06'=>0];
          break;
        case 3:
          $startTrim = $year.'-07-01';
          $endTrim = $year.'-10-01';
          $monthEmpty = ['07'=>0,'08'=>0,'09'=>0];
          break;
        case 4:
          $startTrim = $year.'-10-01';
          $endTrim = ($year+1).'-01-01';
          $monthEmpty = ['10'=>0, '11'=>0, '12'=>0];
          break;
      }
      $sqlBooks->where('date','<',$endTrim)->where('date','>=',$startTrim );
    } else {
      $sqlBooks->whereYear('date','=',$year);
    }
    
    
    if ($site>0){
      $allRooms = \App\Rooms::where('site_id',$site)->pluck('id');
      $sqlBooks->whereIn('room_id',$allRooms);
    }
    
    
    
    $books = $sqlBooks->get();
     
    $monthBook = $monthProp = $result = $base = $iva = $tIva = $monthEmpty;

    if ($books)
    foreach ($books as $b){
      $monthBook[substr($b->date,5,2)] += $b->pvp;
    }

    /************************************************/
    
      
    $booksByYear = \App\Book::where('type_book', 2)
              ->whereYear('start', '=', $year)
              ->get();

//    $oPayments = \App\Expenses::getPaymentToPropYear($year);
    if ($booksByYear)
    foreach ($booksByYear as $p){
      if (isset($monthProp[substr($p->start,5,2)]))
        $monthProp[substr($p->start,5,2)] += $p->get_costProp();
    }
    
    $monthNames = getMonthsSpanish(null, true, true);
    
    /************************************************/
    
    foreach ($monthBook as $k=>$v){
      $result[$k] = $v-$monthProp[$k];
    }
    foreach ($result as $k=>$v){
      $base[$k] = $v/1.1;
    }
    foreach ($base as $k=>$v){
      $iva[$k] = $v*0.1;
    }
    foreach ($result as $k=>$v){
      $tIva[$k] = $base[$k]+$iva[$k];
    }
    $aux = [];
    foreach ($monthNames as $k=>$v){
      if ($k<10 && isset($monthEmpty['0'.$k])) $aux[$k] = $v;
      if ($k>9 && isset($monthEmpty[$k])) $aux[$k] = $v;
    }
    $monthNames =  $aux;
    
    
    return view('backend.revenue.dashboard.anioNatural',[
          'monthBook' => $monthBook,
          'year' =>$year,
          'trim' =>$trim,
          'monthProp' => $monthProp,
          'result' => $result,
          'monthNames' => $monthNames,
          'base' => $base,
          'tIva' => $tIva,
          'iva' => $iva,
          'site' => $site,
          'siteLst'=> \App\Sites::allSitesEnable()
      ]);
    
     dd($monthBook,$monthProp);
  }
}
