<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouterActionsController extends AppController {
  /*   * ************************************************************** */
  /*   * ********     SHORT Functions                       *********** */
  /*   * ************************************************************** */

  public function seasonsDays($val) {
    $seasonDays = \App\SeasonDays::first();
    $seasonDays->numDays = $val;
    if ($seasonDays->save()) {
      return "Cambiado";
    }
  }

  public function nofify($id) {
    $notify = \App\BookNotification::find($id);
    if ($notify->delete()) {
      return view('backend.planning._tableAlertBooking', ['notifications' => \App\BookNotification::all()]);
    }
    return null;
  }

  public function changeSchedule($id, $type, $schedule) {
    $book = \App\Book::find($id);
    if ($type == 1) {
      $book->schedule = $schedule;
    } else {
      $book->scheduleOut = $schedule;
    }
    if ($book->save()) {
      return [
          'status' => 'success',
          'title' => 'OK',
          'response' => "Hora actualizada"
      ];
    }
  }

  function restore($id) {
    $book = \App\Book::find($id);
    $book->type_book = 3;
    if ($book->save()) {
      return [
          'status' => 'success',
          'title' => 'OK',
          'response' => "Reserva restaurada"
      ];
    }
  }

  function getTableRooms() {
    return view('backend.rooms._tableRooms', [
        'rooms' => \App\Rooms::where('state', 1)->orderBy('order', 'ASC')->get(),
        'roomsdesc' => \App\Rooms::where('state', 0)->orderBy('order', 'ASC')->get(),
        'sizes' => \App\SizeRooms::all(),
        'types' => \App\TypeApto::all(),
        'tipos' => \App\TypeApto::all(),
        'owners' => \App\User::all(),
        'show' => 1,
    ]);
  }

  function paymentspro_del($id) {
    if (\App\Paymentspro::find($id)->delete()) {
      return 'ok';
    } else {
      return 'error';
    }
  }

//  function customer_delete($id) {
//    if (\App\Customers::find($id)->delete()) {
//      return 'ok';
//    } else {
//      return 'error';
//    }
//  }
//
  function customer_change($id, $phone) {
    $customer = \App\Customers::find($id);
    $customer->phone = $phone;
    if ($customer->save()) {
      return [
          'status' => 'success',
          'title' => 'OK',
          'response' => "TelÃ©fono cambiado"
      ];
    } else {
      return [
          'status' => 'danger',
          'title' => 'Error',
          'response' => "No se ha cambiado el telÃ©fono"
      ];
    }
  }

  function books_getStripeLink($book, $importe) {
    $book = \App\Book::find($book);
    $import = $importe;
    return view('backend.planning._links', [
        'book' => $book,
        'import' => $import,
    ]);
  }
/*
  function sales_updateLimpBook($id, $importe) {
    $book = \App\Book::find($id);
    
    $cost = $book->cost_total - $book->cost_limp;
    $book->cost_limp = $importe;
    $book->cost_total = $cost + $importe;
    if ($book->save()) {
      return "OK";
    }
  }

  function sales_updateExtraCost($id, $importe) {
    $book = \App\Book::find($id);
    $cost = $book->cost_total - $book->extraCost;
    $book->extraCost = $importe;
    $book->cost_total = $cost + $importe;
    if ($book->save()) {
      return "OK";
    }
  }

  function sales_updateCostApto($id, $importe) {
    $book = \App\Book::find($id);
    $cost = $book->cost_total - $book->cost_apto;
    $book->cost_apto = $importe;
    $book->cost_total = $cost + $importe;
    if ($book->save()) {
      return "OK";
    }
  }

   function sales_updateCostTotal($id, $importe) {
    $book = \App\Book::find($id);
    $book->cost_total = $importe;
    if ($book->save()) {
      return "OK";
    }
  }

  function sales_updatePVP($id, $importe) {
    $book = \App\Book::find($id);
    $book->total_price = $importe;
    if ($book->save()) {
      return "OK";
    }
  }
*/


  function invoices_searchByName($searchString = "") {
    if ($searchString == "") {
      $arraycorreos = array();
      $correosUsuarios = \App\User::all();
      foreach ($correosUsuarios as $correos) {
        $arraycorreos[] = $correos->email;
      }
      $arraycorreos[] = "iankurosaki@gmail.com";
      $arraycorreos[] = "jlargoma@gmail.com";
      $arraycorreos[] = "victorgerocuba@gmail.com";
      $customers = \App\Customers::whereNotIn('email', $arraycorreos)->where('email', '!=', ' ')
                      ->distinct('email')->orderBy('created_at', 'DESC')->get();
    } else {
      $customers = \App\Customers::where('name', 'LIKE', '%' . $searchString . '%')
                      ->orWhere('email', 'LIKE', '%' . $searchString . '%')->get();
      echo "asdfasdf";
    }
    $arrayIdCustomers = array();
    foreach ($customers as $customer) {
      $arrayIdCustomers[] = $customer->id;
    }
    $books = \App\Book::where('type_book', 2)->whereIn('customer_id', $arrayIdCustomers)->orderBy('start', 'DESC')
            ->paginate(25);
    return view('backend.invoices._table', ['books' => $books,]);
  }

  function gastos_delete($id) {
    if (\App\Expenses::find($id)->delete()) {
      return 'ok';
    } else {
      return 'error';
    }
  }

  function ingresos_delete($id) {
    if (\App\Incomes::find($id)->delete()) {
      return 'ok';
    } else {
      return 'error';
    }
  }

  function cashbox_updateSaldoInicial($id, $type, $importe) {
    $cashbox = \App\Cashbox::find($id);
    $cashbox->import = $importe;
    if ($cashbox->save()) {
      return "OK";
    }
  }

  function bank_updateSaldoInicial($id, $type, $importe) {
    $cashbox = \App\Cashbox::find($id);
    $cashbox->import = $importe;
    if ($cashbox->save()) {
      return "OK";
    }
  }

  function days_secondPay_update($id, $days) {
    $day = \App\DaysSecondPay::find($id);
    $day->days = $days;
    $day->save();
  }
  
  function processData() {
    \Artisan::call('ProcessData:all');
  }
  
  function clearCookies() {
    unset($_COOKIE["XSRF-TOKEN"]);
    unset($_COOKIE["laravel_session"]);
    return redirect('/login');
  }
  
}
