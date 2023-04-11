<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use PDF;
use \Carbon\Carbon;
use Auth;
use App\Invoices;


class InvoicesController extends AppController {

  public function index($order = null) {
    $year = $this->getActiveYear();
    $startYear = $year->start_date;
    $endYear = $year->end_date;
    $sql_invoices = Invoices::where('date', '>', $startYear)
                    ->where('date', '<', $endYear)->with('rooms');
    $orderDate = '';
    if ($order) {
      switch ($order) {
        case "date-desc":
          $sql_invoices->orderBy('date', 'DESC');
          $orderDate = $order;
          break;
        case "date-asec":
          $sql_invoices->orderBy('date', 'ASC');
          $orderDate = $order;
          break;
      }
    } else {
      $sql_invoices->orderBy('created_at', 'DESC');
    }
    $invoices = $sql_invoices->get();
    $totalValue = $invoices->sum('total_price');
    $isMobile = config('app.is_mobile');
    
    $sites = \App\Sites::allSites();
    return view('backend/invoices/index', compact('invoices', 'totalValue', 'orderDate', 'isMobile','sites'));
  }
  
  
  
  public function getData($oInvoice){
    $sites = \App\Sites::allSites();
    if ($oInvoice){
      $emisores = $oInvoice->emisor();
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize ($items);
      
      $siteID = $oInvoice->site_id;
            
      return ['oInvoice'=>$oInvoice,'items'=>$items,'emisores'=>$emisores,'emisor'=>$oInvoice->emisor,'sites'=>$sites,'siteID'=>$siteID];
    }
    $oInvoice = new Invoices();
    $emisores = $oInvoice->emisor();
    return ['oInvoice'=>$oInvoice,'items'=>[],'emisores'=>$emisores,'emisor'=>null,'sites'=>$sites,'siteID'=>null];
  }
  
  public function update($id){
    $oInvoice = Invoices::find($id);
    return view('backend/invoices/forms/_form',$this->getData($oInvoice)); 
  }
  public function update_modal($book_id){
    $oInvoice = Invoices::where('book_id',$book_id)->first();
    $data = $this->getData($oInvoice);
    $data['book_id'] = $book_id;
    
    $oBook = \App\Book::find($book_id);
    
    if (!$data['items'] || count($data['items']) == 0){
      
      
      $extrasAsig = \App\BookExtraPrices::getDynamicWithExtr($book_id);
      $totalExtras = 0;
      $itemsExtr = [];
      foreach ($extrasAsig as $e){
        $totalExtras += $e->price;
        $itemsExtr[] = [
          'detail'=> $e->extra->name,
          'iva'   => 10,
          'price' => $e->price,
        ];
      }
    
        
      $detail = 'Alojamiento ';
      
      $oRoomType = $oBook->room->RoomsType()->first();
      if ($oRoomType){
        $detail .= '('.$oRoomType->title.')';
      }
//      $detail .= PHP_EOL.'*Fechas: del '. convertDateToShow_text($oBook->start,true);
//      $detail .= ' al '. convertDateToShow_text($oBook->start,true).'*';
      
      $data['items'][] = [
        'detail'=> $detail,
        'iva'   => 10,
        'price' => $oBook->total_price-$totalExtras,
      ];
      
      foreach ($itemsExtr as $v){
        $data['items'][] = $v;
      }
    }
    $oInvoice = $data['oInvoice'];
    if (!$oInvoice->id){
      $customer = $oBook->customer;
      $email= (trim($customer->email_notif) == '') ? $customer->email : $customer->email_notif;
//      dd($customer);
      $oInvoice->name = $customer->name;
      $oInvoice->email = $email;
      $oInvoice->nif  = $customer->DNI;
      $oInvoice->address = $customer->address;
      $oInvoice->phone   = $customer->phone;
      $oInvoice->zip_code= $customer->zipCode;
    }
    
    if (!$data['emisor']){
      switch ($oBook->room->site_id){
        case 1:$data['emisor']='riad';break;
        case 2:$data['emisor']='rosa';break;
        case 5:$data['emisor']='siloe';break;
      }
    }
    
    
    return view('backend/invoices/forms/_form-modal',$data); 
  }
  
  public function save(Request $request){
    
    $id = $request->input('id',null);
    $items = $request->input('item',[]);
    $iva = $request->input('iva',[]);
    $prices = $request->input('price',[]);
          
    $oBook = null;
    $book_id = $request->input('book_id',null);
    if ($book_id){
        $oBook = \App\Book::find($book_id);
    }
        
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    if (!$oInvoice){
        $oInvoice = new Invoices();
        $oInvoice->date = date('Y-m-d');
        $oInvoice->code = 'RIAD'.date('y');
        if ($oBook){
            $oInvoice->book_id = $book_id;
            $oInvoice->site_id = $oBook->room->site_id;
        }
        $nextNumber = Invoices::select('number')
                ->withTrashed()
                ->whereYear('date','=',date('Y'))
                ->orderBy('number','desc')->first();
        if ($nextNumber){
          $oInvoice->number = $nextNumber->number+1;
        } else $oInvoice->number = 1;
    } else {
      $book_id = $oInvoice->book_id;
      if ($book_id){
          $oBook = \App\Book::find($book_id);
      }
      
    }
    
    $emisor = $request->input('emisor',null);
    $aEmisor = $oInvoice->emisor($emisor);
    if ($emisor && $aEmisor){
      $oInvoice->emisor = $emisor;
      $oInvoice->name_business = $aEmisor['name'];
      $oInvoice->nif_business  =  $aEmisor['nif'];
      $oInvoice->address_business  = $aEmisor['address'];
      $oInvoice->phone_business    = $aEmisor['phone'];
      $oInvoice->zip_code_business = $aEmisor['zipcode'];
      $oInvoice->reat_business = $aEmisor['REAT'];
      $oInvoice->url_business = $aEmisor['url'];
      $oInvoice->site_id =$aEmisor['site_id'];
    }
    
    
    $oInvoice->name = $request->input('name');
    $oInvoice->nif  = $request->input('nif');
    $oInvoice->address = $request->input('address');
    $oInvoice->phone   = $request->input('phone');
    $oInvoice->email   = $request->input('email');
    $oInvoice->zip_code= $request->input('zip_code');
    $oInvoice->total_price= array_sum($prices);
    $oInvoice->save();
    

    $oInvoice->deleteMetaContent('items');
    $invItems = [];
    foreach ($items as $k=>$v){
      $invItems[] = [
        'detail'=> $v,
        'iva'   => isset($iva[$k]) ? $iva[$k] : '',
        'price' => isset($prices[$k]) ? floatVal($prices[$k]) : 0
      ];
      
    }
    if (count($invItems)>0)  
      $oInvoice->setMetaContent('items', serialize ($invItems));
    
    /**********************************/
    $oInvoice->deleteMetaContent('bkg_data');
    $bkg_data = null;
    if ($oBook){
        $bkg_data = [
            'room'  => $oBook->room->nameRoom,
            'pax'   => $oBook->pax,
            'start' => convertDateToShow($oBook->start,true),
            'finish' => convertDateToShow($oBook->finish,true),
          ];
        $oInvoice->setMetaContent('bkg_data', serialize($bkg_data));
    }
    /**********************************/
    
    if ($request->input('confirm',null)) {
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
      $siteData = \App\Sites::siteData($oInvoice->site_id);
      return view('backend/invoices/invoice_confirm', ['oInvoice'=>$oInvoice,'items'=>$items,'site'=>$siteData,'bkg_data'=>$bkg_data]);
    }
    return redirect(route('invoice.edit',$oInvoice->id));
  }

  public function view($id) {
    $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
      $bkg_data = $oInvoice->getMetaContent('bkg_data');
      if ($bkg_data) $bkg_data = unserialize($bkg_data);
      $siteData = \App\Sites::siteData($oInvoice->site_id);
      $url = '/factura/'.encriptID($id).'/'.encriptID($oInvoice->number).'/'.md5($oInvoice->email);
      return view('backend/invoices/invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'site'=>$siteData,'url'=>$url,'bkg_data'=>$bkg_data]);
    }
    
    return back();
  }

  public function donwload_external($id,$num,$email) {
    $id = desencriptID($id);
    $num = desencriptID($num);
    $oInvoice = Invoices::where('id',$id)->where('number',$num)->first();
    if ($oInvoice){
      if (md5($oInvoice->email) == $email){
        $items = $oInvoice->getMetaContent('items');
        if ($items) $items = unserialize ($items);
              
        $bkg_data = $oInvoice->getMetaContent('bkg_data');
        if ($bkg_data) $bkg_data = unserialize($bkg_data);
      
        $numFact = $oInvoice->num;
        $siteData = \App\Sites::siteData($oInvoice->site_id);
        $pdf = PDF::loadView('backend/invoices/invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'site'=>$siteData,'bkg_data'=>$bkg_data]);
        return $pdf->stream('factura-' . $numFact . '-' . str_replace(' ', '-', strtolower($oInvoice->name)) . '.pdf');
      }
    }
    return view('404');
  }
  public function download($id) {
    $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize ($items);
      $bkg_data = $oInvoice->getMetaContent('bkg_data');
      if ($bkg_data) $bkg_data = unserialize($bkg_data);
      $numFact = $oInvoice->num;
      $siteData = \App\Sites::siteData($oInvoice->site_id);
      $pdf = PDF::loadView('backend/invoices/invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'site'=>$siteData,'bkg_data'=>$bkg_data]);
      return $pdf->stream('factura-' . $numFact . '-' . str_replace(' ', '-', strtolower($oInvoice->name)) . '.pdf');
    }
    
    return back();
    
  }

  public function downloadAll($year = '') {
    set_time_limit(0);
    ini_set('memory_limit', '1024M');
    
    $year = $this->getActiveYear();
    $startYear = $year->start_date;
    $endYear = $year->end_date;
    $sql_invoices = Invoices::where('date', '>', $startYear)
                    ->where('date', '<', $endYear);
    $sql_invoices->orderBy('number', 'ASC');
    $oInvoices = $sql_invoices->get();
    $pdf = PDF::loadView('backend/invoices/invoice-lst-pdf', ['oInvoices' => $oInvoices]);
    return $pdf->stream('facturas-' . $year->year . '.pdf');
  }

  public function sendMail(Request $request) {
    
    $id = $request->input('id',null);
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    if ($oInvoice){
      
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
      $bkg_data = $oInvoice->getMetaContent('bkg_data');
      if ($bkg_data) $bkg_data = unserialize($bkg_data);
      $siteData = \App\Sites::siteData($oInvoice->site_id);
      $url = url('/factura/').'/'.encriptID($id).'/'.encriptID($oInvoice->number).'/'.md5($oInvoice->email);
      $content = view('backend/invoices/invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'site'=>$siteData,'url'=>$url,'bkg_data'=>$bkg_data]);
      $to = $oInvoice->email;
      $sended = \Illuminate\Support\Facades\Mail::send('backend.emails.text', ['mailContent' => $content], function ($message) use ($to ) {
          $message->from(config('mail.from.address'));
          $message->to($to);
          $message->subject('Factura');
          $message->replyTo(config('mail.from.address'));
      });
      
      if ($sended){
        return '<p class="alert alert-success">Factura enviada a '.$oInvoice->email.'</p>';
      }
    }
    return '<p class="alert alert-warmimg">Factura no enviada a '.$oInvoice->email.'</p>';
  }
  
  public function delete(Request $request) {
    
    $id = $request->input('id',null);
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $oInvoice->delete();
      return 'OK';
    }

    return 'error';
  }

}
