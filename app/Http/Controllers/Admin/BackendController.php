<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AppController;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class BackendController extends AppController
{
   	public function index()
    {
        return view('backend.home');
    }
    public function migrationCashBank(){
        echo "<pre>";
        foreach( file( public_path('Libro1.csv')) as $key => $line) {

            $data = explode(';', $line);
            $data = array_map("utf8_encode", $data );
            /* Omitimos la cabecera */
            
            if ($key > 0) {
                switch ($data[3]) {
                    case "TARJETA VISA":
                        $typePayment = 0;
                        break;
                    case "CASH JAIME":
                        $typePayment = 1;
                        break;
                    case "CASH JORGE":
                        $typePayment = 2;
                        break;
                    case "BANCO JORGE":
                        $typePayment = 3;
                        break;
                    case "BANCO JAIME":
                        $typePayment = 4;
                        break;
                    default:
                }
                if($data[5] == "GENERICO"){
                    $payfor = null;
                }else{
                    $room =  \App\Rooms::where('nameRoom', 'LIKE', '%'.$data[5].'%')->first();
                    echo $room->id;
                    echo "<br>";
                    $payfor = $room->id;
                }
                

                $expense = new \App\Expenses();
                $expense->concept = $data[1];
                $expense->date = Carbon::createFromFormat('d/m/Y', $data[0]);
                $expense->import = $data[4];
                $expense->typePayment = $typePayment;
                $expense->type =  $data[2];
                $expense->comment = $data[6];
                $expense->Payfor = $payfor; //($data[5] == "GENERICO")? null :$data[5].", ";
                $expense->save();

            }

        }


    }

//    public function migrationCashBank()
//    {
//		 foreach (\App\Payments::All() as $key => $pay) {
//
//
//		 	if ($pay->type == 0 || $pay->type == 1 ) {
//		 		switch ($pay->type) {
//		 			case 0:
//		 				$data['concept'] = $pay->book->customer->name;
//                        $data['typePayment'] = 0;
//		 				break;
//
//		 			case 1:
//		 				$data['concept'] =  $pay->book->customer->name;
//                        $data['typePayment'] = 1;
//		 				break;
//		 		}
//
//		 		$data['date']        = $pay->datePayment;
//		 		$data['import']      = $pay->import;
//		 		$data['comment']     = $pay->comment;
//		 		$data['type']        = 0;
//
//		 		$cashbox = new \App\Cashbox();
//		 		$cashbox->concept = $data['concept'];
//		 		$cashbox->date = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
//		 		$cashbox->import = $data['import'];
//		 		$cashbox->comment = $data['comment'];
//		 		$cashbox->typePayment = $data['typePayment'];
//		 		$cashbox->type = $data['type'];
//		 		if ($cashbox->save()) {
//		 		    echo "Ok CAJA PAGOS<br>";
//		 		}
//		 	}else if ($pay->type == 2 || $pay->type == 3 ) {
//
//		 		switch ($pay->type) {
//		 			case 2:
//		 				$data['concept'] =$pay->book->customer->name;
//                        $data['typePayment'] = 2;
//		 				break;
//
//		 			case 3:
//		 				$data['concept'] = $pay->book->customer->name;
//                        $data['typePayment'] = 3;
//		 				break;
//		 		}
//		 		$data['date']        = $pay->datePayment;
//		 		$data['import']      = $pay->import;
//		 		$data['comment']     = $pay->comment;
//		 		$data['type']        = 0;
//
//		 		$bank = new \App\Bank();
//		 		$bank->concept = $data['concept'];
//		 		$bank->date = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
//		 		$bank->import = $data['import'];
//		 		$bank->comment = $data['comment'];
//		 		$bank->typePayment = $data['typePayment'];
//		 		$bank->type = $data['type'];
//		 		if ($bank->save()) {
//		 		    echo "Ok BANCO PAGOS<br>";
//		 		}
//
//		 	}
//
//		 }
//
//		 foreach (\App\Expenses::All() as $key => $gasto) {
//
//		 	if ($gasto->typePayment == 1 || $gasto->typePayment == 2 ) {
//
//		 		switch ($gasto->typePayment) {
//		 			case 1:
//		 				$data['typePayment'] = 1;
//
//		 				break;
//
//		 			case 2:
//		 				$data['typePayment'] = 0;
//		 				break;
//		 		}
//
//		 		$data['concept']     = $gasto->concept;
//		 		$data['date']        = $gasto->date;
//		 		$data['import']      = $gasto->import;
//		 		$data['comment']     = $gasto->comment;
//		 		$data['type']        = 1;
//
//		 		$cashbox = new \App\Cashbox();
//		 		$cashbox->concept = $data['concept'];
//		 		$cashbox->date = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
//		 		$cashbox->import = $data['import'];
//		 		$cashbox->comment = $data['comment'];
//		 		$cashbox->typePayment = $data['typePayment'];
//		 		$cashbox->type = $data['type'];
//
//		 		if ($cashbox->save()) {
//		 		    echo "Ok CAJA GASTOS<br>";
//		 		}
//
//		 	}
//		 	if ($gasto->typePayment == 0 || $gasto->typePayment == 3 || $gasto->typePayment == 4 ) {
//
//		 		switch ($gasto->typePayment) {
//                    case 0:
//                        $data['typePayment'] = 2;
//                        break;
//                    case 3:
//                        $data['typePayment'] = 2;
//                        break;
//                    case 4:
//                        $data['typePayment'] = 3;
//                        break;
//		 		}
//
//		 		$data['concept']     = $gasto->concept;
//		 		$data['date']        = $gasto->date;
//		 		$data['import']      = $gasto->import;
//		 		$data['comment']     = $gasto->comment;
//		 		$data['type']        = 1;
//
//		 		$bank = new \App\Bank();
//		 		$bank->concept = $data['concept'];
//		 		$bank->date = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
//		 		$bank->import = $data['import'];
//		 		$bank->comment = $data['comment'];
//		 		$bank->typePayment = $data['typePayment'];
//		 		$bank->type = $data['type'];
//		 		if ($bank->save()) {
//		 		    echo "Ok BANCO GASTOS<br>";
//		 		}
//		 	}
//		 }
//    }


    public function insertDNIS()
    {

        foreach( file( public_path('lista-dnis.csv')) as $key => $line) {

            $data = explode(';', $line);
            $data = array_map("utf8_encode", $data );
            /* Omitimos la cabecera */
            if ($key > 0) {

                print_r($data);
                echo "<br>";

                $customers = \App\Customers::where('name', $data[3])->get();
                if (count($customers) > 0){
                    foreach ($customers as $key => $customer){
                        $customer->DNI = $data[4];
                        $customer->save();
                    }
                }

            }

        }


    }
}
