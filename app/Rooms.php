<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\OtaGateway\Config as oConfig;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed nameRoom
 * @property mixed owned
 * @property mixed sizeApto
 * @property mixed typeApto
 * @property mixed minOcu
 * @property mixed maxOcu
 * @property mixed luxury
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed order
 * @property mixed state
 * @property mixed parking
 * @property mixed locker
 * @property mixed cost_cleaning
 * @property mixed price_cleaning
 * @property mixed cost_gift
 * @property mixed price_gift
 *
 * @property HasOne extra
 */
class Rooms extends Model
{
    const GIFT_COST = 5;
    const GIFT_PRICE = 0;
    
    const LUXURY_PRICE = 50;
    const LUXURY_COST = 40;

    const PARKING_PRICE = 20;
    const PARKING_COST = 13.5;

    const CLEANING_MAX_PRICE = 100;
    const CLEANING_MAX_COST = 70;

	public function book()
    {
        return $this->hasMany('\App\Book', 'id', 'room_id');
    }

    public function sizeRooms()
    {
        return $this->hasOne('\App\SizeRooms', 'id', 'sizeApto');
    }

    public function typeAptos()
    {
        return $this->type();
    }

    public function type()
    {
        return $this->hasOne('\App\TypeApto', 'id', 'typeApto');
    }
    
    public function RoomsType()
    {
        return $this->hasOne('\App\RoomsType', 'channel_group', 'channel_group');
    }

    public function user()
    {
        return $this->hasOne('\App\User', 'id', 'owned');
    }

    public function extra()
    {
        return $this->hasOne(Extras::class, 'apartment_size', 'sizeApto');
    }

    public function paymentPro()
    {
        return $this->hasMany('\App\Paymentspro', 'id', 'room_id');
    }

    public static function getPaxRooms($pax, $room)
    {
      $obj = self::select('minOcu')->where('id', $room)->first();
      if ($obj){
        return $obj->minOcu;
      } else {
        return 0;
      }
    }

    public function isAssingToBooking()
    {
        $isAssing = false;
        $books = \App\Book::where('room_id', $this->id)->where('type_book', 9)->get();

        if (count($books) > 0) {
            $isAssing = true;
        } 

        return $isAssing;
        
    }

    public function getCostPropByYear($year)
    {
	    $activeYear = \App\Years::where('year', $year)->first();
	    if (!$activeYear)
	        return 0;
	    $startYear  = new Carbon($activeYear->start_date);
	    $endYear    = new Carbon($activeYear->end_date);

//        $books = \App\Book::whereIn('type_book', [2,7,8])
        $books = \App\Book::where('type_book',2)
                            ->where('room_id', $this->id)
                            ->where('start', '>=', $startYear)
                            ->where('finish', '<=', $endYear)
                            ->orderBy('start', 'ASC')
                            ->get();


        $total = 0;
        $apto = 0;
        $park = 0;
        $lujo = 0;
        foreach ($books as $book) {
          $apto  +=  $book->cost_apto;
          $park  +=  $book->cost_park;
          $lujo  +=  $book->cost_lujo;
        }
        $total += ( $apto + $park + $lujo);

        return $total;


    }

    static function getPvpByYear($year)
    {

      $roomSite = self::pluck('id')->all();
      $total = \App\BookDay::where_type_book_sales(true,true)
                          ->whereYear('date', '=', $year)
                          ->whereIn('room_id',$roomSite)
                          ->sum('pvp');
//      $total = \App\Book::whereIn('type_book', [2,7,8])
//                          ->whereYear('start', '=', $year)
//                          ->whereIn('room_id',$roomSite)
//                          ->sum('total_price');
      return round($total);

    }
    
    static function getPvpLastYears_site($year)
    {
	  
      $allSites = \App\Sites::allSites();
      $bySiteYear = array();
      $year = $year-3;
      foreach ($allSites as $site_id=>$name){
        $bySiteYear[$site_id] = [];
        $roomSite = self::where('site_id',$site_id)->pluck('id')->all();
        for($i=0;$i<4;$i++){
          $aux = $year+$i;
//          $total = \App\Book::whereIn('type_book', [2,7,8])
//                            ->whereYear('start', '=', $aux)
//                            ->whereIn('room_id',$roomSite)
//                            ->sum('total_price');
          $total = \App\BookDay::where_type_book_sales(true,true)
                          ->whereYear('date', '=', $aux)
                          ->whereIn('room_id',$roomSite)
                          ->sum('pvp');
          
          $bySiteYear[$site_id][$aux] = round($total);
        }
      }
      
      $bySite = [];
      foreach ($bySiteYear as $site=>$v){
        $bySite[$site] = implode(',', $v);
      }

      return $bySite;
    }
    
    static function getPvpMonths_site($year)
    {
	  
      $bySiteYear = array();
      $allSites = \App\Sites::allSites();
      foreach ($allSites as $site_id=>$name){
        $bySiteYear[$site_id] = [];
        $roomSite = self::where('site_id',$site_id)->pluck('id')->all();
        for($i=1;$i<13;$i++){
          $bySiteYear[$site_id][$i] = 0;
        }
        
        $books = \App\Book::select('total_price','start')
                ->whereIn('type_book', [2,7,8])
                ->whereYear('start', '=', $year)
                ->whereIn('room_id',$roomSite)->get();
        
        if ($books){
          foreach ($books as $b){
            $bySiteYear[$site_id][date('n', strtotime($b->start))] +=  round($b->total_price);
          }
        }
      }
      
      $bySite = [];
      foreach ($bySiteYear as $site=>$v){
        $bySite[$site] = implode(',', $v);
      }

      return $bySite;
    }

    static function getCostPropByMonth($year,$room_id = NULL)
    {
      
      $existYear = true;
      $year = \App\Years::where('year', $year)->first();
      if (!$year){
        $existYear = false;
        $year = Years::where('active', 1)->first();
      }
      $startYear = new Carbon($year->start_date);
      $endYear = new Carbon($year->end_date);
      $diff = $startYear->diffInMonths($endYear) + 1;
      $lstMonths = lstMonths($startYear,$endYear);
      
      $arrayMonth = [];
      foreach ($lstMonths as $k=>$v){
        $arrayMonth[getMonthsSpanish($v['m'])] = 0;
      }
      
      if (!$existYear){
        return $arrayMonth;
      }
      
      $qry = \App\Book::where('type_book', 2)
            ->where('start', '>=', $startYear)
            ->where('start', '<=', $endYear);
      
      if($room_id){
        $qry->where('room_id',$room_id);
      }
      
      $books = $qry->get();
      $aux= [];
      //get PVP by month
      if ($books){
        foreach ($books as $key => $book) {
          $date = date('n', strtotime($book->start));
          if (!isset($aux[$date])) $aux[$date] = 0;
          $aux[$date] += ($book->cost_apto + $book->cost_park + $book->cost_lujo);
        }
      }
      
      //Load the PVP into Monts list
      foreach ($lstMonths as $k=>$v){
        $month = $v['m'];
        if (isset($aux[$month]))
          $arrayMonth[getMonthsSpanish($month)] = $aux[$month];
      }
      
      
      return $arrayMonth;
    }
    static function getPvpByMonth($year,$room_id = NULL)
    {
      
      $existYear = true;
      $year = \App\Years::where('year', $year)->first();
      if (!$year){
        $existYear = false;
        $year = Years::where('active', 1)->first();
      }
      $startYear = new Carbon($year->start_date);
      $endYear = new Carbon($year->end_date);
      $diff = $startYear->diffInMonths($endYear) + 1;
      $lstMonths = lstMonths($startYear,$endYear);
      
      $arrayMonth = [];
      foreach ($lstMonths as $k=>$v){
        $arrayMonth[getMonthsSpanish($v['m'])] = 0;
      }
      
      if (!$existYear){
        return $arrayMonth;
      }
      
      $qry = \App\Book::where_type_book_sales()
            ->where('start', '>=', $startYear)
            ->where('start', '<=', $endYear);
      
      if($room_id){
        $qry->where('room_id',$room_id);
      }
      
      $books = $qry->get();
      $aux= [];
      //get PVP by month
      if ($books){
        foreach ($books as $key => $book) {
          $date = date('n', strtotime($book->start));
          if (!isset($aux[$date])) $aux[$date] = 0;
          $aux[$date] += $book->total_price;
        }
      }
      
      //Load the PVP into Monts list
      foreach ($lstMonths as $k=>$v){
        $month = $v['m'];
        if (isset($aux[$month]))
          $arrayMonth[getMonthsSpanish($month)] = $aux[$month];
      }
      
      
      return $arrayMonth;
    }

    /**
     * @return float
     */
    public function getCostGiftAttribute()
    {
        return self::GIFT_COST;
    }

    /**
     * @return float
     */
    public function getPriceGiftAttribute()
    {
        return self::GIFT_PRICE;
    }
    
    function Site(){
      return $this->belongsTo('App\Sites', 'site_id', 'id')->first();
    }
    function getURL(){
      return $this->Site()->url.'/fotos/'.$this->nameRoom;
    }
    
/**
   * 
   * @param type $start
   * @param type $finish
   */
  public function getPVP($start,$finish,$pax,$includeCost=false,$all=false) {
    $defaults = $this->defaultCostPrice($start,$finish,$pax);
    
    $priceDay = $defaults['priceDay'];
    $oPrice = \App\DailyPrices::where('channel_group',$this->channel_group)
                ->where('date','>=',$start)
                ->where('date','<',$finish)
                ->get();
   
    
    if ($oPrice) {
        
        foreach ($oPrice as $p) {
          if (isset($priceDay[$p->date]) && $p->price)
            $priceDay[$p->date] = $p->price;
        }
      }
      
    $extra_pax = 0;
    if (($pax>$this->minOcu)){
      $priceExtrPax = \App\Settings::getKeyValue('price_extr_pax');
      if ($priceExtrPax)  $extra_pax = $priceExtrPax*($pax-$this->minOcu);
    }
    $price = 0;
    if ($priceDay){
      foreach ($priceDay as $p) {
        $price +=($p+$extra_pax);
      }
    }
    
    if ($all)  return $priceDay;
    
    if ($includeCost){
      $cost = 0;
      if (is_array($defaults['costDay'])){
        foreach ($defaults['costDay'] as $p) {
          $cost += intval($p);
        }
      }
      return ['price'=>$price,'cost'=>$cost];
    }
    return $price;
  }
  
  public function priceLimpieza($sizeApto) {
    
    if ($sizeApto == 1 || $sizeApto == 5){
      $oExtra = \App\Extras::find(2);
    }
    if ($sizeApto == 2 || $sizeApto == 6 || $sizeApto == 9){
      $oExtra = \App\Extras::find(1);
    }
    if ($sizeApto == 3 || $sizeApto == 4 || $sizeApto == 7 || $sizeApto == 8){
      $oExtra = \App\Extras::find(3);
    }
    
    if (env('APP_APPLICATION') != "riad"){
      if ($this->id == 165 || $this->id == 122){
        $oExtra = \App\Extras::find(6);
      }
    }
    
    if ($oExtra){
      return  [
          'price_limp'=>floatval($oExtra->price),
          'cost_limp'=>floatval($oExtra->cost)
          ];
    } 
    
    return  [
          'price_limp'=>0,
          'cost_limp'=>0
          ];
  }
   
  /**
   * Get the default cost and price to pax and seassons
   * 
   * @param type $start
   * @param type $end
   * @return type
   */
  public function defaultCostPrice($start,$end,$pax) {
    
    $response = ['priceDay'=>null,'costDay'=>null];
    if ($start && $end){
      $startTime = strtotime($start);
      $endTime = strtotime($end);
      $startDate = date('Y-m-d',$startTime);
      $endDate = date('Y-m-d',$endTime);

      $priceDay = [];
      $costDay = [];
      $seassonDay = [];
      $nigths = calcNights($startDate,$endDate);
      
      $dailyCost = round($this->cost/365,2);
      while ($nigths>0){
        $priceDay[date('Y-m-d',$startTime)] = 1000;
        $costDay[date('Y-m-d',$startTime)] = $dailyCost;
        $startTime = strtotime('+1 day', $startTime);
        $nigths--;
      }
      $response = ['priceDay'=>$priceDay,'costDay'=>$costDay];
      /* END: default values by price */
    }
    return $response;
  }
  
  
   /**
   * 
   * @param type $start
   * @param type $finish
   */
  public function getMin_estancia($start,$finish) {

    $minDays = 0;
    
    $minDays = \App\SpecialSegment::getMinStay($start,$finish);
    $oPrice = \App\DailyPrices::where('channel_group',$this->channel_group)
                ->where('date','>=',$start)
                ->where('date','<',$finish)
                ->get();
    if ($oPrice) {
        foreach ($oPrice as $p) {
          if ($p->min_estancia && $p->min_estancia>$minDays)
          $minDays = $p->min_estancia;
        }
      }
    return $minDays;
  }
  
  /**
   * 
   * @param type $start
   * @param type $finish
   */
  static function getListMin_estancia($start) {
    
    $oPrices = \App\DailyPrices::where('date','=',$start)->get();
    $return = null;
    
    if ($oPrices) {
        foreach ($oPrices as $p) {
          $return[$p->channel_group] = $p->min_estancia;
        }
      }
    return $return;
  }
  
  
  static function getRoomsToBooking($quantity, $start, $finish, $sizeApto,$site_id=null){
        $roomSelected   = null;
        
        $qry = Rooms::where('state', 1)
//                ->where('minOcu','<=', $quantity)
                ->where('maxOcu','>=', $quantity);
                
        if ($site_id){
          $qry->where('site_id', $site_id);
        }
        if ($sizeApto){
          $qry->where('sizeApto', $sizeApto);
        }
        $allRoomsBySize = $qry->orderBy('site_id')
                ->orderBy('fast_payment', 'DESC')
                ->orderBy('order_fast_payment', 'ASC')->get();
         
        foreach ($allRoomsBySize as $room){
          $room_id = $room->id;
          if (Book::availDate($start,$finish, $room_id))
          {
            $roomSelected[] = $room;
          }
        }
        
      
        return $roomSelected;
    }
    
  public function getCostRoom($start,$end,$pax) {
    $costDay = $this->defaultCostPrice($start,$end,$pax);
    $cost = 0;
    if ($costDay['costDay']){
      foreach ($costDay['costDay'] as $c){
        $cost +=$c;
      }
    }
    return $cost;
  }
  
  
  
  public function calculateRoomToFastPayment($apto, $start, $finish,$roomID = null) {

    $roomSelected = null;
    $qry = \App\Rooms::
                    where('channel_group', $apto)
                    ->where('state', 1);
        
    if ($roomID) $qry->where('id',$roomID);

    $allRoomsBySize = $qry->orderBy('fast_payment','DESC')
            ->orderBy('order_fast_payment', 'ASC')->get();
    
    foreach ($allRoomsBySize as $room) {
      $room_id = $room->id;
      if (\App\Book::availDate($start, $finish, $room_id)) {
        return $room_id;
      }
    }

    //search simple Rooms to Booking
    $oRoomsGroup = \App\Rooms::select('id')
                    ->where('channel_group', $apto)
                    ->where('state', 1)
                    ->orderBy('fast_payment', 'ASC')
                    ->orderBy('order_fast_payment', 'ASC')->first();
    if ($oRoomsGroup) {
      return $oRoomsGroup->id;
      return ['isFastPayment' => false, 'id' => $oRoomsGroup->id];
    }

    return -1;
  }
  
  public function getPrices_byRange($startDate,$endDate) {
    
    $defaults = $this->defaultCostPrice( $startDate, $endDate,$this->minOcu);
    $priceDay = $defaults['priceDay'];
    $oPrice = \App\DailyPrices::where('channel_group',$this->channel_group)
                ->where('date','>=',$startDate)
                ->where('date','<=',$endDate)
                ->get();
   
    
    if ($oPrice) {
        foreach ($oPrice as $p) {
          if (isset($priceDay[$p->date]) && $p->price)
            $priceDay[$p->date] = $p->price;
        }
      }
      
     return $priceDay;
  }

  static function getRoomsBySite($site_id){
    return Rooms::where('site_id', $site_id)->pluck('name','id');
  }
  
  
  function getDiscount($startDate,$endDate){
    $oPromotions = new \App\Promotions();
    return $oPromotions->getDiscount($startDate,$endDate,$this->channel_group);
  }
  function getPromo($startDate,$endDate){
    $oPromotions = new \App\Promotions();
    return $oPromotions->getPromo($startDate,$endDate,$this->channel_group);
  }
  
  function getRoomPrice($startDate,$endDate,$pax){

    $nigths = calcNights($startDate, $endDate);
    $result = [
        'price_limp'=>0,
        'pvp_init'=>0,'pvp'=>0,
        'discount'=>0,'discount_pvp'=>0,
        'promo_name'=>'','promo_pvp'=>0,
        'discount_name'=>'','PRIVEE'=>0
    ];
    /*------------------------------------*/
    
    $ExtraPrices = \App\ExtraPrices::getFixed($this->channel_group);
    $extraPrice = 0;
    foreach ($ExtraPrices as $e){
        $extraPrice += $e->price;
    }
    $result['price_limp'] = $extraPrice;
    /*------------------------------------*/
    
    $oConfig = new oConfig();
    $pvp = $this->getPVP($startDate, $endDate,$pax);
    $pvp = round($oConfig->priceByChannel($pvp,7,$this->channel_group,false,$nigths),2); //Google Hotels price
    $result['pvp_init'] = $pvp;
    
    // 15% DESCUENTO PROGRAMA PRIVÉE
    $result['PRIVEE'] = round($pvp*0.15);
    $result['discount_pvp'] = $result['PRIVEE'];
    // promociones %
    $disc = $this->getDiscount($startDate,$endDate);
  
    if ($disc['v']>0){
      $result['discount'] = $disc['v'];
      $result['discount_name'] = $disc['n'];
      $result['discount_pvp'] += round($pvp*($disc['v']/100));
      
    }
    $pvp =  round($pvp-$result['discount_pvp']);
    
    // promociones tipo 7x4
    $hasPromo = '';
    $aPromo = $this->getPromo($startDate, $endDate);
    if ($aPromo){
      $promo_nigths = $aPromo['night'];
      $nigths_discount = $promo_nigths-$aPromo['night_apply'];
      $pvp_promo = $pvp;
      if ($promo_nigths>0 && $nigths_discount>0 && $nigths>=$promo_nigths){
        $nigths_ToApply = intval(($nigths/$promo_nigths) * $nigths_discount);
        $pvpAux = round( ($pvp/$nigths) * ($nigths-$nigths_ToApply) , 2);
        $result['promo_name'] = $aPromo['name'];
        $result['promo_pvp'] = round(($pvp - $pvpAux),2);
        $pvp = $pvpAux;
      }
    }
    // promociones tipo 7x4  
          
    $result['pvp'] = round($pvp + $extraPrice,2);
    return $result;
  }
  
  static function RoomsCH_IDs($ch){
    return self::where('channel_group',$ch)
        ->where('state',1)->pluck('id')->toArray();
  }
  
  static function availSite($sID){
    return Rooms::where('site_id',$sID)
            ->where('channel_group','!=','')->count();
      
  }
  
}
