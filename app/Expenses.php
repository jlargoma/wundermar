<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
  
  protected $table = 'expenses';
  
  static function getTypes(){
    return [
        'agencias' => 'AGENCIAS',
        'alquiler' => 'ALQUILER INMUEBLES',
        'amenities' => 'AMENITIES',
        'comisiones' => 'COMISIONES COMERCIALES',
        'comision_tpv' => 'COMSION TPV',
        'equip_deco' => 'EQUIPACION Y DECORACION',
        'bancario' => 'GASTOS BANCARIOS',
        'representacion' => 'GASTOS REPRESENTACION',
        'impuestos' => 'IMPUESTOS',
        'lavanderia' => 'LAVANDERIA',
        'limpieza' => 'LIMPIEZA',
        'publicidad' => 'MARKETING Y PUBLICIDAD',
        'mensaje' => 'MENSAJERIA',
        'prop_pay' => 'PAGO PROPIETARIOS',
        'seguros' => 'PRIMAS SEGUROS',
        'excursion' => 'PROVEEDORES EXCURSIÓN',
        'mantenimiento' => 'REPARACION Y CONSERVACION',
        'seg_social' => 'SEG SOCIALES',
        'serv_prof' => 'SERVICIOS PROF INDEPENDIENTES',
        'sueldos' => 'SUELDOS Y SALARIOS',
        'suministros' => 'SUMINISTROS',
        'sabana_toalla' => 'TEXTIL Y MENAJE',
        'parking_mercado_san_agustin' => 'PARKING MERCADO SAN AGUSTIN',
        'parking_puerta_real' => 'PARKING PUERTA REAL',
        'varios' => 'VARIOS',
    ];
  }
  static function getTypesParking(){
    return [
      'PKM_SAN_AGUSTIN' => 'parking_mercado_san_agustin',
      'PKM_RIAD' => 'parking_puerta_real', 
    ];
  }
  static function getTypesGroup(){
    return [
            'names'=> [
              'agencias' => 'AGENCIAS',
              'alquiler' => 'ALQUILER INMUEBLES',
              'comision_tpv' => 'COMSION TPV',
              'limpieza' => 'LAVANDERIA Y LIMPIEZA',
              'prop_pay' => 'PAGO PROPIETARIOS',
              'otros' => 'RESTO GASTOS',
              'empleados' => 'SUELDOS Y SEG SOCIAL',
              'suministros' => 'SUMINISTROS',
              'parkings' => 'PARKINGS',
                
            ],
            'groups' => [
                'agencias' => 'agencias',
                'alquiler' => 'alquiler',
                'comision_tpv' => 'comision_tpv',
                'lavanderia' => 'limpieza',
                'limpieza'   => 'limpieza',
                'prop_pay'   => 'prop_pay',
                'seg_social' => 'empleados',
                'sueldos'    => 'empleados',
                'suministros'=> 'suministros',
                'parking_mercado_san_agustin'=> 'parkings',
                'parking_puerta_real'=> 'parkings',
                'excursion'=> 'otros',
            ]];
        
  }
  
  static function getTypesImp(){
    return [
        'agencias' => 'AGENCIAS',
        'alquiler' => 'ALQUILER INMUEBLES',
        'amenities' => 'AMENITIES',
        'comisiones' => 'COMISIONES COMERCIALES',
        'excursion' => 'PROVEEDORES EXCURSIÓN',
        'comision_tpv' => 'COMSION TPV',
        'equip_deco' => 'EQUIPACION Y DECORACION',
        'bancario' => 'GASTOS BANCARIOS',
        'representacion' => 'GASTOS REPRESENTACION',
        'lavanderia' => 'LAVANDERIA',
        'limpieza' => 'LIMPIEZA',
        'publicidad' => 'MARKETING Y PUBLICIDAD',
        'mensaje' => 'MENSAJERIA',
        'excursion' => 'PROV. EXCURSIÓN',
        'serv_prof' => 'SERVICIOS PROF INDEPENDIENTES',
        'suministros' => 'SUMINISTROS',
        'sabana_toalla' => 'TEXTIL Y MENAJE',
        'varios' => 'VARIOS',
    ];
  }
  
  //Para poner nombre al tipo de cobro//
  static function getTypeCobro($typePayment=NULL) {
    $array = [
        0 => "Tarjeta visa",//"Metalico Jorge",
        2 => "CASH",// "Metalico Jaime",
        3 => "Banco",//"Banco Jorge",
    ];

    if (!is_null($typePayment)) return $typePayment = $array[$typePayment];
    
    return $array;
  }
    
    
  static function getListByRoom($start,$end,$roomID){
    return self::where('date', '>=', $start)
            ->Where('date', '<=', $end)
            ->Where('PayFor', 'LIKE', '%' . $roomID. '%')       
            ->orderBy('date', 'DESC')
            ->get();
  }
  
  static function getTypesOrderned(){
    $types =  [
      'alquiler'=>"ALQUILER INMUEBLES",
      'comisiones'=>"COMISIONES COMERCIALES", // COMISIONES COMERCIALES</option>
      'comision_tpv'=>"COMSION TPV",
      'equip_deco' => 'EQUIPACION Y DECORACION',
//      'decoracion'=>"DECORACIóN", // DECORACION</option>
//      'equi_vivienda'=>"EQUIPAMIENTO VIVIENDA", // EQUIPAMIENTO VIVIENDA</option>
      'bancario'=>"GASTOS BANCARIOS", // GASTOS BANCARIOS</option>
      'impuestos'=>"IMPUESTOS", // IMPUESTOS</option>
      'lavanderia'=>"LAVANDERIA", // LAVANDERIA</option>
      'limpieza'=>"LIMPIEZA", // LIMPIEZA</option>
      'publicidad'=>"MARKETING Y PUBLICIDAD", // MARKETING Y PUBLICIDAD</option>
//      'mensaje'=>"MENSAJERIA", // MENAJE</option>
      'prop_pay'=>"PAGO PROPIETARIOS", //PAGO PROPIETARIO</option>
      'regalo_bienv'=>"AMENITIES", // REGALO BIENVENIDA</option>
      'mantenimiento'=>"REPARACION Y CONSERVACION", // REPARACION Y CONSERVACION</option>
      'sabana_toalla'=>"TEXTIL Y  MENAJE", // SABANAS Y TOALLAS</option>
      'seg_social'=>"SEG SOCIALES", // SEG SOCIALES</option>
      'serv_prof'=>"SERVICIOS PROF INDEPENDIENTES", // SERVICIOS PROF INDEPENDIENTES</option>
      'sueldos'=>"SUELDOS Y SALARIOS", // SUELDOS Y SALARIOS</option>
      'suministros'=>"SUMINISTROS", 
      'seguros'=>"PRIMAS SEGUROS", 
      'representacion'=>"GASTOS REPRESENTACION", 
      'amenities'=>"AMENITIES", 
      'varios'=>"VARIOS", // VARIOS</option>
      'excursion' => 'PROVEEDORES EXCURSIÓN',
        
    ];
    
    $aux = [];
    foreach ($types as $k=>$v){
      $aux[] = $k;
    }
    
    sort($aux);
    
    foreach ($aux as $k=>$v){
      echo "'".$v."' => '".$types[$v]."',<br/>";
    }
    die;
  }
  
    
  static function getExpensesBooks(){
    return [
//        'prop_pay'      => 0,
        'alquiler'      => 0,
        'agencias'      => 0,
        'comision_tpv'  => 0,
        'limpieza'      => 0,
        'lavanderia'    => 0,
        'amenities'     => 0,
        'mantenimiento' => 0,
        'excursion' => 0,
      ];
    
  }
  static function getTotalByYear($year){
   return self::whereYear('date', '=', $year)
                    ->sum('import');
  }
  
}
