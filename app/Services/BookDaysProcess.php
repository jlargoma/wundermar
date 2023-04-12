<?php

namespace App\Services;
use App\Book;
use App\BookDay;
use App\Years;
use Illuminate\Support\Facades\DB;
/**
 * 
 */
class BookDaysProcess
{
    public function process() {
      $arrErrors = [];
      $cYear = date('Y');
      $oYear = Years::where('year', $cYear-1)->first();
      if ($oYear){
        $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
        if (count($error)>0) $arrErrors[] = (implode(',', $error));
      }
      $oYear = Years::where('year', $cYear)->first();
      if ($oYear){
        $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
        if (count($error)>0) $arrErrors[] = (implode(',', $error));
      }
      $oYear = Years::where('year', $cYear+1)->first();
      if ($oYear){
        $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
        if (count($error)>0) $arrErrors[] = (implode(',', $error));
      }

      return $arrErrors;
    }
}