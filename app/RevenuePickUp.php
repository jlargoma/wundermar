<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevenuePickUp extends Model
{
  protected $table = 'revenue_pickup';
  
              
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
    $tOcup = $this->ocupacion+$this->llegada;
    if ($tOcup==0) return 0;
    return round($this->ingresos / $tOcup);
  }
  /**
   * @return int
   */
  function get_libre(){
    return $this->disponibilidad-($this->ocupacion+$this->llegada);
  }
  /**
   * @return int
   */
  function get_ocup_percent(){
    $tOcup = $this->ocupacion+$this->llegada;
    if ($this->disponibilidad==0) return 0;
    return (round( $tOcup*100/$this->disponibilidad)).'%';
  }
}