<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixCosts extends Model
{
    protected $table = 'fix_costs';
//    public $timestamps = false;
    
  static function getLst(){
    return [
        'alq'=>'ALQUILER',
        'serv'=>'LUZ / AGUA /INTERNET',
        'seg'=>'SEGURO',
        'manten'=>'MANTENIMIENTO Y REPARACIONES',
        'repos'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
        'limp'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
        'pers'=>'PERSONAL ESPECÍFICO',
        'imput'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
        'varios'=>'VARIOS',
    ];
    return [
      0=>[],
      1=>[
          'riad_1'=>'ALQUILER',
          'riad_2'=>'LUZ / AGUA /INTERNET',
          'riad_3'=>'SEGURO',
          'riad_4'=>'MANTENIMIENTO Y REPARACIONES',
          'riad_5'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
          'riad_6'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
          'riad_7'=>'PERSONAL ESPECÍFICO',
          'riad_8'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
          'riad_9'=>'VARIOS',
      ],
      2=>[
          'rosa_1'=>'ALQUILER',
          'rosa_2'=>'LUZ / AGUA /INTERNET',
          'rosa_3'=>'SEGURO',
          'rosa_4'=>'MANTENIMIENTO Y REPARACIONES',
          'rosa_5'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
          'rosa_6'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
          'rosa_7'=>'PERSONAL ESPECÍFICO',
          'rosa_8'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
          'rosa_9'=>'VARIOS',
      ],
      3=>[
           'glr_1'=>'ALQUILER',
          'glr_2'=>'LUZ / AGUA /INTERNET',
          'glr_3'=>'SEGURO',
          'glr_4'=>'MANTENIMIENTO Y REPARACIONES',
          'glr_5'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
          'glr_6'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
          'glr_7'=>'PERSONAL ESPECÍFICO',
          'glr_8'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
          'glr_9'=>'VARIOS',
      ],
      4=>[
           'elv_1'=>'ALQUILER',
          'elv_2'=>'LUZ / AGUA /INTERNET',
          'elv_3'=>'SEGURO',
          'elv_4'=>'MANTENIMIENTO Y REPARACIONES',
          'elv_5'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
          'elv_6'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
          'elv_7'=>'PERSONAL ESPECÍFICO',
          'elv_8'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
          'elv_9'=>'VARIOS',
      ],
      5=>[
          'siloe_1'=>'ALQUILER',
          'siloe_2'=>'LUZ / AGUA /INTERNET',
          'siloe_3'=>'SEGURO',
          'siloe_4'=>'MANTENIMIENTO Y REPARACIONES',
          'siloe_5'=>'REPOSICIÓN MUEBLES / MÁQUINAS',
          'siloe_6'=>'PRODUCTOS LIMPIEZA Y AMENITIES',
          'siloe_7'=>'PERSONAL ESPECÍFICO',
          'siloe_8'=>'IMPUTACIÓN CTE FIJO ESTRUCTURADA',
          'siloe_9'=>'VARIOS',
      ],
        
    ];
  }
  
  
  static function getByYear($year,$siteID){
    return \App\FixCosts::where('year',$year)
            ->where('site_id',$siteID)
            ->get();
  }
  
  static function deleteByYear($year,$siteID){
    return \App\FixCosts::where('year',$year)
            ->where('site_id',$siteID)
            ->delete();
  }
}
