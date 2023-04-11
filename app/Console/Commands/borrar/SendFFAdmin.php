<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\Models\Forfaits\Forfaits;
use App\Models\Forfaits\ForfaitsOrders;
use App\Models\Forfaits\ForfaitsOrderItem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Settings;
use Carbon\Carbon;

class SendFFAdmin extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'sendFFAdmin:sendForfaits';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Find the forfaits in checkin to send to the person in charge of the collection';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    if (config('app.appl') == 'riad')
      return;
    $this->checkInStatus();
  }

  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {

    $tomorrow = Carbon::now()->addDay(1)->format('Y-m-d'); 
    //
    $allForfaits = Book::select('*', 'forfaits.id as forfaits_id ')
            ->where('start',$tomorrow)->where('type_book', 2)
                    ->join('forfaits', 'book.id', '=', 'forfaits.book_id')
                    ->orderBy('start', 'ASC')->get();
    

    $lstOrders = [];

    $listDetail = [];

    if ($allForfaits) {
      $oForfait = new Forfaits();
      foreach ($allForfaits as $forfait) {

        $hasForfaits = false;
        $c_name = $email = $phone = ' -- ';
        $customer = $forfait->customer;
        if ($customer) {
          $c_name = $customer->name;
          $email = $customer->email;
          $phone = $customer->phone;
        }

        
        //BEGIN: Obtengo el detalle de la orden
        
        $allOrders = ForfaitsOrders::where('forfats_id', $forfait->forfaits_id)
                        ->where('status', '2')->whereNull('quick_order')->pluck('id')->all();



        $ordersItems = ForfaitsOrderItem::whereIn('order_id', $allOrders)
                ->where('type', 'forfaits')
                ->whereNull('cancel')
                ->get();

        $totalForf = $price_wdForf = $qForf = 0;
        $text_ff = '';
        foreach ($ordersItems as $ffItem) {
          $hasForfaits = true;
          $ffUsr = json_decode($ffItem->data);
          if (isset($ffUsr)) {
            foreach ($ffUsr as $usr) {
              $qForf++;
              $text_ff .= '<tr><td>'
                      . 'Forfaits ' . $usr->days . ' Dias'
                      . '<br/>' . $usr->typeTariffName
                      . '<br/>Edad: ' . $usr->age
                      . '<br/>Inicio: ' . $usr->dateFrom
                      . '<br/>Fin: ' . $usr->dateTo
                      . '</td><td  class="tcenter">1</td><td class="tright">' . $usr->price . '€</td></tr>';
            }
          }
          $insurances = json_decode($ffItem->insurances);
          if (isset($insurances)) {
            foreach ($insurances as $insur) {
              $text_ff .= '<tr><td>'
                      . $oForfait->getInsurName($insur->insuranceId)
                      . '<br/>' . $insur->clientName
                      . '<br/>DNI: ' . $insur->clientDni
                      . '<br/>Inicio: ' . $insur->dateFrom
                      . '<br/>Fin: ' . $insur->dateTo
                      . '</td><td class="tcenter">1</td><td class="tright">' . $insur->price . '€</td></tr>';
            }
          }

          if ($ffItem->extra > 0) {
            $text_ff .= '<tr><td>Extra'
                    . '</td><td class="tcenter">1</td><td class="tright">' . $ffItem->extra . '€</td></tr>';
          }
          $totalForf += $ffItem->total;
          $price_wdForf += $ffItem->price_wd;
        }
        
       


        $allOrders = ForfaitsOrders::where('forfats_id', $forfait->forfaits_id)
                        ->where('status', '2')->where('type','forfaits')->get();
        

        foreach ($allOrders as $order){
          $hasForfaits = true;
          $text_ff .= '<tr><td>Orden rápida<br>'.$order->detail
                    . '</td><td class="tcenter">'.$order->quantity.'</td><td class="tright">' . $order->total . '€</td></tr>';
          $totalForf += $order->total;
          $price_wdForf += $order->total;
          $qForf +=$order->quantity;
        }
        
        if ($totalForf != $price_wdForf){
              $text_ff .= '<tr>
                      <td ><b>SubTotal Forfaits </b></td>
                      <td class="center" style="text-decoration:line-through;">'.number_format($price_wdForf, 2).'€</td>
                      <td class="tright">'.number_format($totalForf, 2).'€</td>
                    </tr>';
          } else {
              $text_ff .= '<tr>
                      <td colspan="2"><b>SubTotal Forfaits </b></td>
                      <td class="tright">'.number_format($totalForf, 2).'€</td>
                    </tr>';
        }
        
        if ($hasForfaits){
          $listDetail[] = [
            'tit'=>''.$c_name.' | Entrada el '.$forfait->start,
            'content' => $text_ff,
            'total' =>  $totalForf,
            'total_wd' =>$price_wdForf
          ];
        }
         
        //END: Obtengo el detalle de la orden


        //BEGIN: detalle de la reserva
        
        if ($hasForfaits){  
          $lstOrders[] = '<tr>'
                . '<td>'.$c_name.'<br/>'.$email.'</td>'
                . '<td>'.$phone.'</td>'
                . '<td>'.$forfait->pax.'</td>'
                . '<td>'.$forfait->room->nameRoom.'</td>'
                . '<td>'.convertDateToShow($forfait->start).' - '.convertDateToShow($forfait->finish) .'</td>'
                . '<td class="tcenter">'.$qForf.'</td>'
                . '<td class="tcenter">'.$totalForf.'€</td>'
                . '</tr>';
        }
      
        
        
                 
        //END: detalle de la reserva

      }
    }

    if (count($lstOrders) > 0)
      $this->sendMessage($lstOrders,$listDetail);
  }

  private function sendMessage($lstOrders,$lstOrdersDetail) {
    $subject = 'Forfaits para retirar hoy';

    $mailContent = '<h3>Las siguientes reservas deben retirarse de ForfaitExpress:</h3>';

    
    
    $mailContent .= '<table class="forfait"><tr class="forfaitHeader">'
                . '<th>Cliente</th>'
                . '<th>Teléfono</th>'
                . '<th>Pax</th>'
                . '<th>Apart.</th>'
                . '<th>In / Out</th>'
                . '<th>Cant. Forfaits</th>'
                . '<th>Total Forfaits</th>'
                . '</tr>';
    
        foreach ($lstOrders as $item){
          $mailContent.=$item;
        }
    
    $mailContent .='</table>';
    
    
    $mailContent .= '<h3>Detalles:</h3>';
    
    foreach ($lstOrdersDetail as $items){
      $mailContent .= '<h4>'.$items['tit'].'</h4>';
      $mailContent .= '<table class="forfait">'
               . '<tr class="forfaitHeader">'
               . '<th>Item</th>'
               . '<th>Cantidad</th>'
               . '<th>Precio</th>'
               . '</tr>'
               . $items['content']
               .'</table>';
      

   
      
    }
    $mailsTo = ['forfait@miramarski.com','alquilerapartamentosmiramarski@gmail.com'];
    Mail::send('backend.emails.forfait', [
        'mailContent' => $mailContent,
        'title' => $subject
            ], function ($message) use ($subject,$mailsTo) {
      $message->from(config('mail.from.address'));
      $message->to($mailsTo);
      $message->subject($subject);
    });
  }

}
