<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use Excel;
use \Carbon\Carbon;

class CustomersController extends AppController {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $list = [];
    $customers = $this->getCustomersList();
    if ($customers) {
      foreach ($customers as $c) {
        $list[] = [
            'id' => $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'seasson' => $c->seasson,
        ];
      }
    }
    $aCustomersRequest = \App\CustomersRequest::getCustomersLst();
    if ($aCustomersRequest) {
      foreach ($aCustomersRequest as $c) {
        $list[] = [
            'id' => 'temp_' . $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'seasson' => date('Y', strtotime($c->created_at))
        ];
      }
    }
    $list = json_decode(json_encode($list));
    return view('backend/customers/index', [
        'customers' => $list]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request) {

    $customer = new \App\Customers();

    $customer->user_id = Auth::user()->id;
    $customer->name = $request->input('name');
    $customer->email = $request->input('email');
    $customer->phone = $request->input('phone');
    $customer->comments = $request->input('comment');

    if ($customer->save()) {
      return redirect()->action('CustomersController@index');
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id) {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update($id) {
    $customer = \App\Customers::find($id);

    return view('backend/customer/_form', [
        'customer' => $customer
    ]);
  }

  public function save(Request $request) {
    $id = $request->input('id');
    $customerUpadate = \App\Customers::find($id);


    $customerUpadate->name = $request->input('name');
    $customerUpadate->email = $request->input('email');
    $customerUpadate->phone = $request->input('phone');
    $customerUpadate->DNI = $request->input('dni');
    $customerUpadate->address = $request->input('address');
    $customerUpadate->comments = $request->input('comments');
    if ($customerUpadate->save()) {
      echo "Usuario cambiado!!";
    } else {
      echo "Error";
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function delete($id) {
    if (strpos($id, 'temp') === FALSE) {
      $customer = \App\Customers::find($id);
      if ($customer->delete()) {
        return 'ok';
      }
    } else {
      $id = str_replace('temp_', '', $id);
      $customer = \App\CustomersRequest::find($id);
      if ($customer->delete()) {
        return 'ok';
      }
    }
    return 'error';
  }

  public function createExcel() {
    \Excel::create('Clientes', function($excel) {


      $clientes = $this->getCustomersList(true);

      $excel->sheet('Clientes', function($sheet) use($clientes) {


        $sheet->freezeFirstColumn();

        $sheet->row(1, [
            'Número', 'Nombre', 'Email', 'Telefono', 'temporada'
        ]);

        $index = 0;
        foreach ($clientes as $user) {

          $sheet->row($index + 2, [
              $user->id, $user->name, $user->email, $user->phone, $user->seasson
          ]);
          $index++;
        }
      });
    })->export('xlsx');
  }

  function searchByName($searchString = "") {
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
    }


    $list = [];

    if ($customers) {
      foreach ($customers as $c) {
        $list[] = [
            'id' => $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'seasson' => $c->seasson,
        ];
      }
    }
    $aCustomersRequest = \App\CustomersRequest::where('name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchString . '%')->get();
    if ($aCustomersRequest) {
      foreach ($aCustomersRequest as $c) {
        $list[] = [
            'id' => 'temp_' . $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'seasson' => date('Y', strtotime($c->created_at))
        ];
      }
    }
    $list = json_decode(json_encode($list));


    return view('backend.customers._table', ['customers' => $list]);
  }

  /**
   * Get CustomersID by airbnb or booking
   * 
   * @param type $startYear
   * @param type $endYear
   * @return type
   */
  private function getNotClients($startYear, $endYear) {

    //airbnb/" => 4,
    //booking/" => 1

    $lst = array();
    $booksCollection = \App\Book::select('customer_id')->where('start', '>=', $startYear)
                    ->where('start', '<=', $endYear)
                    ->whereIn('agency', [1, 4])->get();

    if ($booksCollection) {
      foreach ($booksCollection as $b)
        $lst[] = $b->customer_id;
    }

    return $lst;
  }

  private function getCustomersList($all = false) {
    $arraycorreos = array();
    $correosUsuarios = \App\User::all();

    foreach ($correosUsuarios as $correos) {
      $arraycorreos[] = $correos->email;
    }

    $arraycorreos[] = "iankurosaki17@gmail.com";
    $arraycorreos[] = "jlargoma@gmail.com";
    $arraycorreos[] = "victorgerocuba@gmail.com";


    $seasons = array();
    $seasonslst = \App\Years::all();
    foreach ($seasonslst as $s) {
      $seasons[$s->year] = [strtotime($s->start_date), strtotime($s->end_date)];
    }

    $emails = \App\Customers::select('email')
                    ->distinct()
                    ->where('email', '!=', " ")
                    ->where('email', '!=', "-")->get();

    $qry = \App\Customers::select('customers.id', 'customers.created_at', 'email', 'name', 'phone', 'book.id as book_id')
            ->whereNotIn('email', $arraycorreos)
            ->whereIn('email', $emails->toArray())
            ->leftJoin('book', function ($join) {
      $join->on('customers.id', '=', 'book.customer_id')->whereIn('agency', [1, 4]);
    });

    if (!$all) {
      $year = $this->getActiveYear();
      $startYear = new Carbon($year->start_date);
      $endYear = new Carbon($year->end_date);
      $qry->where('customers.created_at', '>=', $startYear)->where('customers.created_at', '<=', $endYear);
    }

    $customers = $qry->orderBy('customers.created_at', 'DESC')->get();
    $lst = [];
    if ($customers) {
      foreach ($customers as $c) {
        if (is_null($c->book_id)) {

          if (!isset($lst[$c->email])) {
            $c->seasson = null;
            if ($c->created_at) {
              $c->seasson = substr($c->created_at, 0,4);
            }
            $lst[$c->email] = $c;
          }
        }
      }
    }
    $aCustomersRequest = \App\CustomersRequest::all();
    if ($aCustomersRequest) {
      foreach ($aCustomersRequest as $c) {
        $aux = [
            'id' => 'temp_' . $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'seasson' => date('Y', strtotime($c->created_at))
        ];
        
        $list[$c->email] = json_decode(json_encode($aux));
      }
    }

    ksort($lst);
    return ($lst);
  }

  public function sendMailsTest(Request $request) {
    $mail = Mail::send([], [], function ($message) {
              $message->from(config('mail.from.address'));
              $message->to('cy50z82f4pkzgh2j@guest.airbnb.com');
              $message->subject('Reservas');
              // here comes what you want
              $message->setBody('-'); // assuming text/plain
              // or:
              $message->setBody('--', 'text/html'); // for HTML rich messages
            });
  }

}
