<?php

namespace App\Services;
use App\Settings;
use App\Book;
use App\BookDay;
use Illuminate\Support\Facades\DB;
/**
 * 
 */
class RevenueService
{
    public $response;
    public $start;
    public $finish;
    public $days;
    public $months;
    public $books;
    public $booksSQL;
    public $booksOcup;
    public $type_book;
    public $rChannel;
    public $rSite;
    public $aSites;
    public $mDays;


    public function __construct()
    {
      $this->start = null;
      $this->finish = null;
      $this->books = null;
      $this->booksOcup = null;
      $this->type_book = BookDay::get_type_book_sales(true,true);
    }
    
    function setDates($month,$year,$oYear=null) {
      if ($month<13 && $month>0){
        
        $d1 = new \DateTime($year.'-'.$month.'-01');
        $d2 = clone $d1;
        $d1->modify('first day of this month');
        $d2->modify('last day of this month');
        
        $this->start  = $d1->format("Y-m-d");
        $this->finish = $d2->format("Y-m-d");
        $this->days = ($d1->diff($d2))->days+1;
      } else {
        $d1 = new \DateTime($oYear->start_date);
        $d2 = new \DateTime($oYear->end_date);
        $d1->modify('first day of this month');
        $d2->modify('last day of this month');
        
        $this->start  = $d1->format("Y-m-d");
        $this->finish = $d2->format("Y-m-d");
        $this->days = ($d1->diff($d2))->days+1;
      }
      
      $this->months = getMonthsSpanish(null,true,true);
      unset($this->months[0]);
    }
    
    function setBook($type=null) {
      //-----------------------------------------------------------//
      if ($type == 'pki'){ die;
        $this->booksSQL = Book::where_type_book_sales(true,true)
                ->where('start', '>=', $this->start)
                ->where('start', '<=', $this->finish);
        $this->books = $this->booksSQL->get();
        return;
      }
      //-----------------------------------------------------------//
//      $this->books = Book::where_book_times($this->start,$this->finish)
//              ->whereIn('type_book',$this->type_book)->get();
      $this->booksSQL = BookDay::where('date', '>=', $this->start)
              ->where('date', '<=', $this->finish)
              ->whereIn('type',$this->type_book);
      $this->books = $this->booksSQL->get();
    }
    
    function setRooms() {
      $rooms = \App\Rooms::all();
      $this->aSites = \App\Sites::allSitesKey();
      $aux = $aux2 = [];
      foreach ($rooms as $r){
        if(isset($this->aSites[$r->site_id])){
          $aux[$r->id] = $r->channel_group;
          $aux2[$r->id] = $r->site_id;
        }
      }
      
      $this->rChannel = $aux;
      $this->rSite = $aux2;
    }
    
    function getExtras(){
      /// BEGIN: Extras
      $months_extras = array();
      for($i=0;$i<13;$i++) $months_extras[$i] = 0;
    
      $extTyp = \App\ExtraPrices::getTypes();
      $oExtras = \App\ExtraPrices::getDynamic();

      $extrasList =  array();
      $extraTit   = array();
      foreach ($oExtras as $item){
        $extrasList[$item->id] = $months_extras;
        $extraTit[$item->id] = $item->name;
      }
      $extrasGroup =  array();
      foreach ($extTyp as $k=>$v){
        $extrasGroup[$k] = $months_extras;
      }

      $extras = Book::where_book_times($this->start,$this->finish)
              ->whereIn('type_book',$this->type_book)
              ->select('book_extra_prices.price','book_extra_prices.type','start','extra_id')
              ->Join('book_extra_prices','book_extra_prices.book_id','=','book.id')
                ->where('book_extra_prices.deleted',0)
                ->get();
      if($extras){
        foreach ($extras as $e){
          $m = date('n', strtotime($e->start));
          $months_extras[$m] += $e->price;
          $months_extras[0] += $e->price;

          $type = isset($extrasGroup[$e->type]) ? $e->type : 'others';

          $extrasGroup[$type][$m] += $e->price;
          $extrasGroup[$type][0] += $e->price;
          if (isset($extrasList[$e->extra_id])){
            $extrasList[$e->extra_id][$m] += $e->price;
            $extrasList[$e->extra_id][0] += $e->price;
          }
        } 
      }
      return [$extrasList,$months_extras,$extraTit];
    }
    
    function getExpenses($year){
      $m = array();
      for($i=0;$i<13;$i++) $m[$i] = 0;
      
      $lstG = $m;
      $gastos = \App\Expenses::whereYear('date', '=', $year)
                    ->orderBy('date', 'DESC')->get();
      if($gastos){
        foreach ($gastos as $g){
          $lstG[date('n', strtotime($g->date))] += $g->import;
        }
        foreach ($lstG as $k=>$g){
          $lstG[$k] = round($g);
        }
      }
      return $lstG;
    }
    function getTotalExpenses($year){
      return \App\Expenses::whereYear('date', '=', $year)
                    ->sum('import');
    }
    
    function getIngrSite($data){
      $aux = [];
      if($data){
        foreach ($data as $k=>$d){
//          if ($k == 4) continue;
          unset($d['months'][0]);
          $aux[$d['t']] = $d['months'];
        }
      }
      return $aux;
    }
    function getIngrMonths($data){
      $aux = [];
      if($data){
        foreach ($data as $k=>$d){
          unset($d['months'][0]);
          foreach ($d['months'] as $k=>$v)
            $aux[$k] = isset ($aux[$k]) ? $aux[$k]+$v : $v;
        }
      }
      return $aux;
    }
    
    
//    function setBookOcupacion($start,$end) {
//      $this->booksOcup = Book::where_book_times($start,$end)
//              ->whereIn('type_book',$this->type_book)->get();
//    }
    
    
    function countNightsSite() {
      $lst = $this->books;
      $result = [];
      foreach ($this->aSites as $k=>$v) $result[$k] = 0;
      foreach ($lst as $b){
        if (isset($this->rSite[$b->room_id])){
          $site = $this->rSite[$b->room_id];
          $result[$site]++;
        } else{
          echo $b->book_id.' ';
        }
      }
      return $result;
    }
    
    function countBookingsSite() {
      $lst = $this->books;
      $result = [];
      $books  = [];
      foreach ($this->aSites as $k=>$v) $result[$k] = 0;
      foreach ($lst as $b){
        if (!in_array($b->book_id, $books)){
          $books[] = $b->book_id;
          if (isset($this->rSite[$b->room_id])){
            $site = $this->rSite[$b->room_id];
            $result[$site]++;
          }
        }
      }
      return $result;
    }
    function countBookingsSiteMonths() {
      $lst = $this->books;
      $result = [];
      $books  = [];
      $auxMonths = [0=>0];
      foreach ($this->months as $k2=>$v2)  $auxMonths[$k2] = 0;
      $result[0] = $auxMonths;
      foreach ($this->aSites as $k=>$v) $result[$k] = $auxMonths;
      foreach ($lst as $b){
        if (!in_array($b->book_id, $books)){
          $books[] = $b->book_id;
          if (isset($this->rSite[$b->room_id])){
            $site = $this->rSite[$b->room_id];
            $am = date('n',strtotime($b->date));
            $result[$site][$am]++;
            $result[$site][0]++;
          } 
        }
      }
      foreach ($result as $S=>$months)
        foreach ($months as $k=>$v)  
          $result[0][$k] += $v;

      return $result;
    }
    
    function getRatios($year = null){
      $ar = ['p'=>0,'n'=>0,
          't_s'=>0,// Total PVP Semana
          'c_s'=>0,// Total Noches Semana
          't_f'=>0,// Total PVP fin de semana (viernes&sabado)
          'c_f'=>0,// Total Noches fin de semana (viernes&sabado)
          ];

      $aux = [0=>$ar];
      foreach ($this->months as $k2=>$v2)  $aux[$k2] = $ar;
      $aRatios = [0=>$aux];
      foreach ($this->aSites as $k => $v){
        $aux = [0=>$ar];
        foreach ($this->months as $k2=>$v2)  $aux[$k2] = $ar;
        $aRatios[$k] = $aux;
      }

      foreach ($this->books as $b){
        $siteID = isset($this->rSite[$b->room_id]) ? $this->rSite[$b->room_id] : 0;
        if($siteID == 0) continue;
        $am = date('n',strtotime($b->date));
        $aRatios[$siteID][$am]['p']+= $b->pvp;
        $aRatios[$siteID][$am]['n']++;
        $day = date('w', strtotime($b->date));
        if ($day<5){
          $aRatios[$siteID][$am]['c_s']++;
          $aRatios[$siteID][$am]['t_s'] += $b->pvp;
        } else {
          $aRatios[$siteID][$am]['c_f']++;
          $aRatios[$siteID][$am]['t_f'] += $b->pvp;
        }
      }
      foreach ($aRatios as $k=>$v){
        $aux = $aux2 = 0;
        $auxc_s = $auxt_s = $auxc_f = $auxt_f = 0;
        foreach ($aRatios[$k] as $k2=>$v2){
          $aux  += $v2['p'];
          $aux2 += $v2['n'];
          $auxc_s += $v2['c_s'];
          $auxt_s += $v2['t_s'];
          $auxc_f += $v2['c_f'];
          $auxt_f += $v2['t_f'];
          $aRatios[0][$k2]['p'] += $v2['p'];
          $aRatios[0][$k2]['n'] += $v2['n'];
          $aRatios[0][$k2]['c_s'] += $v2['c_s'];
          $aRatios[0][$k2]['t_s'] += $v2['t_s'];
          $aRatios[0][$k2]['c_f'] += $v2['c_f'];
          $aRatios[0][$k2]['t_f'] += $v2['t_f'];
        }

        $aRatios[$k][0] = [
            'p'=>$aux,'n'=>$aux2,
            'c_s'=>$auxc_s,'t_s'=>$auxt_s,
            't_f'=>$auxt_f,'c_f'=>$auxc_f,
            ];
      }

      $aux = $aux2 = 0;
      $auxc_s = $auxt_s = $auxc_f = $auxt_f = 0;
      
      foreach ($aRatios[0] as $k=>$v){
        $aux  += $v['p'];
        $aux2 += $v['n'];
        $auxc_s += $v['c_s'];
        $auxt_s += $v['t_s'];
        $auxc_f += $v['c_f'];
        $auxt_f += $v['t_f'];
      }
      $aRatios[0][0] = ['p'=>$aux,'n'=>$aux2,
           'c_s'=>$auxc_s,'t_s'=>$auxt_s,
            't_f'=>$auxt_f,'c_f'=>$auxc_f];
      return $aRatios;
    }
    
    function createDaysOfMonths($year){
      $this->mDays = [0=>365];
      foreach ($this->months as $k=>$v){
        $this->mDays[$k] = cal_days_in_month(CAL_GREGORIAN, $k,$year);
      }
    }
    
    function getIncomesYear($year){
      return \App\Incomes::getIncomesYear($year);
    }
    

    function getMonthSum($field,$filter,$date1,$date2) {
      $lst = DB::select('SELECT new_date,room_id, SUM('.$field.') as total '
            . ' FROM ('
            . '        SELECT '.$field.',room_id,DATE_FORMAT('.$filter.', "%m-%y") new_date '
            . '        FROM book'
            . '        WHERE type_book IN ('.implode(',',$this->type_book).')'
            . '        AND '.$filter.' >= "'.$date1.'" '
            . '        AND '.$filter.' <= "'.$date2.'" '
            . '      ) AS temp_1 '
            . ' GROUP BY temp_1.room_id,temp_1.new_date'
            );
    
      foreach ($this->aSites as $k=>$v) $result[$k] = [];
      
      foreach ($lst as $v){
        if (isset($this->rSite[$v->room_id])){
          $sID = $this->rSite[$v->room_id];
          if (!isset($result[$sID][$v->new_date])) 
            $result[$sID][$v->new_date] = 0;
            
          $result[$sID][$v->new_date] += $v->total;
        }
      }
      
      foreach ($result as $S=>$months)
        foreach ($months as $k=>$v){
          if (!isset($result[0][$k])) $result[0][$k] = 0;
          $result[0][$k] += $v;
        }
      
      return $result;
    }
    
    
    function commisionTPVBookingsSiteMonths() {
      $lst = $this->books;
      $result = [];
      $books  = [];
      $auxMonths = [0=>0];
      foreach ($this->months as $k2=>$v2)  $auxMonths[$k2] = 0;
      
      $bIDSite = [];
      foreach ($lst as $b){
        if (!in_array($b->book_id, $books)){
          $books[] = $b->book_id;
          if (isset($this->rSite[$b->room_id])){
            $site = $this->rSite[$b->room_id];
            $bIDSite[$site][] = $b->book_id;
          } 
        }
      }
      
      
      foreach ($this->aSites as $k=>$v){
        $result[$k] = $aux = $auxMonths;
        if (isset($bIDSite[$k])){
          $payments = \App\BookOrders::where('paid',1)->whereIn('book_id',$bIDSite[$k])
              ->groupBy('updated_at')->selectRaw('sum(amount) as sum, updated_at')->pluck('sum','updated_at');
          foreach ($payments as $d=>$p){
            $aux[date('n',strtotime($d))] += $p;
          }
          foreach ($aux as $d=>$p){
            $result[$k][$d] = round(paylandCost($p/100));
          }
        }
        
      }
      
      foreach ($result as $S=>$months)
        foreach ($months as $k=>$v){
          if (!isset($result[0][$k])) $result[0][$k] = 0;
          $result[0][$k] += $v;
        }
      return $result;
    }
    
    function getADR_finde() {
      $lst = $this->books;
      $result = [];
      foreach ($this->aSites as $k=>$v) $result[$k] = [
          't_s'=>0,// Total PVP Semana
          'c_s'=>0,// Total Noches Semana
          't_f'=>0,// Total PVP fin de semana (viernes&sabado)
          'c_f'=>0,// Total Noches fin de semana (viernes&sabado)
          ];
      
      $result[0] = [
          't_s'=>0,// Total PVP Semana
          'c_s'=>0,// Total Noches Semana
          't_f'=>0,// Total PVP fin de semana (viernes&sabado)
          'c_f'=>0,// Total Noches fin de semana (viernes&sabado)
          ];
      
      foreach ($lst as $b){
        if (isset($this->rSite[$b->room_id])){
          $site = $this->rSite[$b->room_id];
          $day = date('w', strtotime($b->date));
          if ($day<5){
            $result[$site]['c_s']++;
            $result[$site]['t_s'] += $b->pvp;
          } else {
            $result[$site]['c_f']++;
            $result[$site]['t_f'] += $b->pvp;
          }
        }
      }
      foreach ($this->aSites as $k=>$v){
        $result[0]['c_s'] += $result[$k]['c_s'];
        $result[0]['t_s'] += $result[$k]['t_s'];
        $result[0]['c_f'] += $result[$k]['c_f'];
        $result[0]['t_f'] += $result[$k]['t_f'];
      }
      
      return $result;
    }
    
    
    function comparativaAnual($year){
      
      $startInic = $this->start;
      $startFinish = $this->finish;
      $auxMonths = [0=>0];
      foreach ($this->months as $k2=>$v2)  $auxMonths[$k2] = 0;
      
      $t1 = [0=>0];
      $t2 = [0=>$auxMonths];
      foreach ($this->aSites as $k=>$v){
        $t1[$k] = 0;
        $t2[$k] = $auxMonths;
      }
      
      for($i=0;$i<5;$i++){
        $aux_year = $year-$i;
        
        $totalAnual[$aux_year] = $totalYear = $t1;
        $totalYearMonth = $t2;
        $oYear = \App\Years::where('year', $aux_year)->first();
        if (!$oYear) continue;
        $this->start  = $oYear->start_date;
        $this->finish = $oYear->end_date;
        $this->setBook();
        $nigths = $pvp = 0;
       
        foreach ($this->books as $b){
          if (isset($this->rSite[$b->room_id])){
            $am = date('n',strtotime($b->date));
            $site = $this->rSite[$b->room_id];
            $totalYearMonth[$site][$am] += $b->pvp;
            $totalYearMonth[0][$am] += $b->pvp;
          }
          $nigths++;
          $pvp += $b->pvp;
        }
        
        foreach ($this->aSites as $k=>$v){
          $auxTotal = array_sum($totalYearMonth[$k]);
          $totalYear[$k] = $auxTotal;
          $totalYearMonth[$k][0] = $auxTotal;
        }
        
        $auxTotal = array_sum($totalYearMonth[0]);
        $totalYearMonth[0][0] = $auxTotal;
        $totalYear[0] = array_sum($totalYear);
        
        $totalAnual[$aux_year] = $totalYear;
        $totalAnual[$aux_year]['months']  = $totalYearMonth;
        $totalAnual[$aux_year]['nigths'] = $nigths;
        $totalAnual[$aux_year]['pvp'] = $pvp;
      }
      
      $this->start = $startInic;
      $this->finish= $startFinish;
      $this->setBook();
      return $totalAnual;
    }
}