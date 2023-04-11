<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
  protected $table = 'revenue';
  
  
//     'day'            => $day,
//              'ocupacion'      => $v,
//              'disponibilidad' => $dispByCh[$ch],
//              'channel'        => $ch,
//              'ingresos'       => $ingresos[$day][$ch],
                   
  /*

   * RevPar= Ingresos por habitación / Habitaciones disponibles
   * or
   * RevPar = Tarifa promedio diaria de habitaciones * Tasa de Ocupación
   
   * 
   * 
  */
  
  function get_RevPar(){
    if ($this->disponibilidad==0) return 0;
    return round($this->ingresos / $this->disponibilidad);
  }
  
  /**
   * ADR = Ingreso total por habitaciones / Total habitaciones ocupadas
   * @return int
   */
  function get_ADR(){
    if ($this->ocupacion==0) return 0;
    return round($this->ingresos / $this->ocupacion);
  }
}