<?php

namespace App\Services\Bookings;

use App\Rooms;
use App\Book;
/**
 * Description of GetBooksLimp
 *
 * @author cremonapg
 */
class GetBooksLimp {

  public function __construct() {
  }

  static function getBooks($startYear,$endYear) {
      
    //      1 => 'Reservado - stripe',
    //      2 => 'Pagada-la-señal',
    //      4 => 'Bloqueado',
    //      7 => 'Reserva Propietario',
    //      8 => 'ATIPICAS',
    //     11 => 'blocked-ical',

    $noRooms = \App\Rooms::where('channel_group','')->pluck('id');
    $sqlBooks =Book::where('finish', '>=', $startYear)
                    ->where('finish', '<=', $endYear)
                    ->whereNotIn('room_id',$noRooms)
                    ->whereIn('type_book', [1,2,4,7,8,11]);
    $noCustomer = \App\Customers::whereIn('id',$sqlBooks->pluck('customer_id'))
            ->where('name','Bloqueo automatico')->pluck('id');

    return $sqlBooks->whereNotIn('customer_id',$noCustomer)
            ->orderBy('finish', 'ASC')->get();
  }
}
