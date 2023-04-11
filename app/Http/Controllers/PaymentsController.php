<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Carbon\Carbon;
use App\Traits\BookEmailsStatus;

class PaymentsController extends AppController
{
   use BookEmailsStatus;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        return view('backend/payments/index',[
                                                'pagos' => \App\Payments::all(),
                                                'book' => new \App\Book(),
                                                ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payment = new \App\Payments();
        
        $date = Carbon::createFromFormat('d-m-Y' ,$request->date);
        $payment->book_id = $request->id;
        $payment->datePayment = $date;
        $payment->import = $request->importe;
        $payment->comment = $request->comment;
        $payment->type = $request->type;
        
        $saved = $payment->save();
        
        //Send PAyment Notification
        if ($saved && $request->importe>0){
          
          
          $book = \App\Book::find($request->id);
          if (in_array($book->type_book, [1,9,11,99])){
            $book->type_book = 2;
            $book->save();
          }
          
          if ($book->customer->send_notif){
            $subject = translateSubject('RECIBO PAGO RESERVA',$book->customer->country);
            $subject .= ' '. $book->customer->name;
            $this->sendEmail_confirmCobros($book,$subject,floatval($request->importe),$book->customer->email_notif);
          }
          if ($request->type == 0){
            $siteID = \App\Rooms::select('site_id')->where('id',$book->room_id)->first();
            \App\Incomes::generateFromBook($request->importe,$request->comment,$book->id,$siteID->site_id,$payment->id );
          }
          
        }
 

        if ($saved) {
            return 'cobro guardado';
        }
            
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $paymentUpdate = \App\Payments::find($request->id);

        $paymentUpdate->import = $request->importe;
        if ($paymentUpdate->save()) {
          \App\Incomes::updFromBook($paymentUpdate->import,$paymentUpdate->id);
           return "Importe cambiado";
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
